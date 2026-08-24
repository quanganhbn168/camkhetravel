<?php

namespace App\Filament\Resources\Comments\Schemas;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Project;
use App\Models\Landing;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nội dung bình luận')
                ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                ->schema([
                    MorphToSelect::make('commentable')
                        ->label('Nội dung được bình luận')
                        ->types([
                            MorphToSelect\Type::make(Post::class)->titleAttribute('title'),
                            MorphToSelect\Type::make(Project::class)->titleAttribute('title'),
                            MorphToSelect\Type::make(Landing::class)->titleAttribute('title'),
                        ])
                        ->disabled()
                        ->columnSpanFull(),
                    TextInput::make('author_name')->label('Họ tên')->required()->maxLength(120),
                    TextInput::make('author_email')->label('Email')->email()->maxLength(255),
                    Select::make('status')->label('Trạng thái')->options(Comment::statusOptions())->required(),
                    DateTimePicker::make('approved_at')->label('Duyệt lúc'),
                    Textarea::make('body')->label('Bình luận')->required()->rows(6)->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
