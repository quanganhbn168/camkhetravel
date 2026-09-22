<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('commentable_type')
                    ->label('Loại')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Comment::commentableTypeLabels()[$state] ?? 'Khác'),
                TextColumn::make('commentable.title')->copyable()->copyMessage('Đã sao chép')->label('Nội dung')->searchable()->wrap()->limit(45),
                TextColumn::make('author_name')->copyable()->copyMessage('Đã sao chép')->label('Người gửi')->searchable()->sortable(),
                TextColumn::make('author_email')->label('Email')->searchable()->toggleable(),
                TextColumn::make('rating')->label('Đánh giá')->formatStateUsing(fn (?int $state): string => $state ? $state.'/5' : '—')->sortable(),
                TextColumn::make('body')->label('Bình luận')->wrap()->lineClamp(2)->limit(80),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Comment::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Comment::STATUS_APPROVED => 'success',
                        Comment::STATUS_PENDING => 'warning',
                        Comment::STATUS_SPAM, Comment::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Gửi lúc')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(Comment::statusOptions()),
                SelectFilter::make('commentable_type')->label('Loại nội dung')->options(Comment::commentableTypeLabels()),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->label('Duyệt')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record): bool => $record->status !== Comment::STATUS_APPROVED)
                    ->action(fn (Comment $record) => $record->approve())
                    ->successNotificationTitle('Đã duyệt bình luận'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
