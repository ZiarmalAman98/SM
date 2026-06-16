<?php

namespace App\Filament\Pages;

use Monzer\FilamentChatifyIntegration\Pages\Chatify as BaseChat;

class Chats extends BaseChat
{
    public static function getNavigationLabel(): string
    {
        return __('Chat');
    }

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $slug = "Chat";
    protected static ?string $navigationLabel = "Chat";
    protected static ?string $title = "Chat";
}