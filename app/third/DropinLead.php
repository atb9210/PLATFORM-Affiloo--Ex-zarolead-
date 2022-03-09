<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class DropinLead extends Model
{
    protected $table = 'third_dropin_leads';

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'status', 'info', 'notes', 'hash',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public static $statuses = [
        'complaint',
        'refunded',
        'unprocessed',
        'shipped',
        'delivered',
        'return',
        'stock',
        'confirmed',
        'no answer',
        'cancelled',
        'fake',
        'duplicate',
        'to recall',
        'to do',
        'waiting payment',
        'abandoned',
    ];

    public static function findByHash(string $hash)
    {
        $results = DropinLead::where('hash', $hash)->get();
        if ($results->count() > 0) {
            return $results[0];
        }
        return null;
    }

    private function getChatAPIClient()
    {
        $settings = \App\Settings::get();
        $apiInstance = env('CHATAPI_INSTANCE', '');
        $apiToken = env('CHATAPI_TOKEN', '');
        if (isset($settings['chatapi']['api_instance'])) {
            $apiInstance = $settings['chatapi']['api_instance'];
        }
        if (isset($settings['chatapi']['api_token'])) {
            $apiToken = $settings['chatapi']['api_token'];
        }
        $chatapi = new \Driverfury\ChatAPI\ChatAPI($apiInstance, $apiToken);
        return $chatapi;
    }

    public function refreshWhatsappInfo()
    {
        $messages = [];
        $lastMessage = null;
        if ($this->phone !== null && $this->phone != '') {
            $chatapi = $this->getChatAPIClient();
            $messages = $chatapi->getMessages($this->phone);
        }
        $messagesCount = count($messages);
        if ($messagesCount > 0) {
            $lastMessage = $messages[$messagesCount - 1];
        }
        if ($lastMessage !== null) {
            $info = $this->info;
            if ($info === null) {
                $info = [];
            }
            if (!isset($info['whatsapp'])) {
                $info['whatsapp'] = [
                    'last_message' => null,
                    'last_message_read' => null,
                ];
            }
            $info['whatsapp']['last_message'] = $lastMessage['id'];
            $this->info = $info;
            $this->save();
        }
    }

    public function sendMessage(string $message): bool
    {
        $result = false;
        if ($this->phone !== null && $this->phone != '' && $message != '') {
            $chatapi = $this->getChatAPIClient();
            $obj = $chatapi->sendMessage($this->phone, $message);
            $result = ($obj !== null);
        }
        return $result;
    }
}
