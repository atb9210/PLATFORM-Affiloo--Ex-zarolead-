<?php

namespace Driverfury\ChatAPI;

use Driverfury\Requests\Requests;

class ChatAPI
{
    protected string $apiUrl = 'https://api.chat-api.com';
    protected string $apiInstance = '';
    protected string $apiToken = '';
    protected Requests $http;

    public function __construct(string $apiInstance, string $apiToken)
    {
        $this->apiInstance = $apiInstance;
        $this->apiToken = $apiToken;
        $this->http = new Requests();
    }

    public function sendMessage(string $phone, string $message): array|null
    {
        $object = null;

        $data = [
            'phone' => $phone,
            'body' => $message,
        ];
        $data = json_encode($data);
        $url = $this->apiUrl . '/instance' . $this->apiInstance . '/message?token=' . $this->apiToken;
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
                    $object = [
                        'id' => $response['id'],
                    ];
                }
            }
        } catch (Exception $e) {
            // Nothing
        }
        return $object;
    }

    private function phoneNumberToChatId(string $phone): string
    {
        $email = '';
        for ($i = 0; $i < strlen($phone); $i += 1) {
            if (ctype_digit($phone[$i])) {
                $email .= $phone[$i];
            }
        }
        $email .= '@c.us';
        return $email;
    }

    public function getMessages(string $phone): array
    {
        $messages = [];

        try {
            $headers = [
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ];
            $chatId = $this->phoneNumberToChatId($phone);
            $params = [
                'token' => $this->apiToken,
                'chatId' => $chatId,
                'limit' => 100, // TODO: What about the limit?
                'last' => true,
            ];
            $response = $this->http->get($this->apiUrl . '/instance' . $this->apiInstance . '/messages', $headers, $params);
            if ($response['code'] == 200 && isset($response['data']['messages'])) {
                $messages = $response['data']['messages'];
            }
        } catch (\Exception $e) {
            // Nothing
        }

        return $messages;
    }
}
