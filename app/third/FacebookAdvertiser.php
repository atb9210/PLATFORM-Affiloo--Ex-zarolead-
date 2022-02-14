<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use Driverfury\Facebook\Facebook;

class FacebookAdvertiser extends Model
{
    protected $table = 'third_facebook_advertisers';

    protected $fillable = [
        'name', 'access_token',
    ];

    public function getClient(): Facebook
    {
        return new Facebook($this->access_token);
    }

    public function checkAccessToken(): bool
    {
        $fb = $this->getClient();
        $res = $fb->checkAccessToken();
        if (!$res) {
            // Send order to telegram
            $telegram = new \Longman\TelegramBot\Telegram(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_USERNAME'));
            \Longman\TelegramBot\Request::initialize($telegram);
            $chatId = -576211838; // TODO: Save this into .env?
            $telegramMessage = $this->name . ' - Scaduto token';
            \Longman\TelegramBot\Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $telegramMessage,
            ]);
        }
        return $res;
    }
}
