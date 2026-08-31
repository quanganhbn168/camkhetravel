<?php

namespace App\Filament\Bni\Resources\BniArticleComments;

use App\Filament\Bni\Resources\BniArticleComments\Pages\ManageBniArticleComments;
use App\Models\BniArticle;
use App\Models\Comment;
use App\Support\Bni\BniPanelAccess;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BniArticleCommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationLabel = 'Bình luận tin BNI';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return 'Cộng đồng & vận hành';
    }

    public static function getModelLabel(): string
    {
        return 'bình luận tin BNI';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bình luận tin BNI';
    }

    public static function canViewAny(): bool
    {
        return BniPanelAccess::canManageChapterContent();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('commentable_type', (new BniArticle)->getMorphClass())
            ->with(['commentable.chapter', 'user']);

        if (BniPanelAccess::isChapterManager() && ! BniPanelAccess::canManageEverything()) {
            $query->whereHasMorph(
                'commentable',
                [BniArticle::class],
                fn (Builder $articleQuery): Builder => $articleQuery->where('bni_chapter_id', BniPanelAccess::chapterId() ?? 0),
            );
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nội dung bình luận')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->schema([
                    TextInput::make('author_name')->label('Hội viên')->required()->maxLength(120)->columnSpanFull(),
                    TextInput::make('author_email')->label('Email')->email()->maxLength(255),
                    Select::make('status')->label('Trạng thái')->options(Comment::statusOptions())->required(),
                    Textarea::make('body')->label('Nội dung')->required()->rows(5)->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('commentable.title')->label('Tin BNI')->wrap(),
                TextColumn::make('commentable.chapter.short_name')->label('Chapter')->badge()->placeholder('Tin sự kiện'),
                TextColumn::make('author_name')->label('Hội viên')->searchable()->sortable(),
                TextColumn::make('body')->label('Nội dung')->limit(80)->wrap()->searchable(),
                TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn (string $state): string => Comment::statusOptions()[$state] ?? $state),
                TextColumn::make('created_at')->label('Gửi lúc')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(Comment::statusOptions()),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Comment $record): bool => $record->status !== Comment::STATUS_APPROVED)
                    ->action(fn (Comment $record) => $record->approve()),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBniArticleComments::route('/')];
    }
}
