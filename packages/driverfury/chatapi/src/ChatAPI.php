<?php

namespace Driverfury\ChatAPI;

class ChatAPI
{
    protected string $apiUrl = 'https://api.chat-api.com/';
    protected string $apiInstance = '';
    protected string $apiToken = '';

    public function __construct(string $apiInstance, string $apiToken)
    {
        $this->apiInstance = $apiInstance;
        $this->apiToken = $apiToken;
    }

    public function sendMessage(string $phone, string $message): bool
    {
        $data = [
            'phone' => $phone,
            'body' => $message,
        ];
        $data = json_encode($data);
        $url = $this->apiUrl . 'instance' . $this->apiInstance . '/message?token=' . $this->apiToken;
        $options = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $data,
            ]
        ]);
        try {
            $response = file_get_contents($url, false, $options);
            if ($response !== null && $response !== '') {
                $response = json_decode($response, true);
                if (isset($response['sent']) && $response['sent'] === true) {
                    return true;
                }
            }
        } catch (Exception $e) {
            // Nothing
        }
        return false;
    }
}
