<?php

namespace ziya\Proxy;

/**
 * Class BotApi
 * @package app\telegram
 * @property $message
 * @property $callback_query
 * @property int $chat_id
 * @property int $message_id
 * @property mixed $update
 */
class TelegramApi
{
    public $last_update = 0;

    private $token;

    public function __construct($token = null)
    {
        $this->token = $token;
    }

    public function setLastUpdate($id)
    {
        $this->last_update = ($id+1);
    }

    public function getUpdates()
    {
        $params = [
            'offset'=>$this->last_update
        ];
        return $this->query('getUpdates', $params);
    }


    public function query($method, $data = [])
    {

        array_walk($data, function (&$value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
        });

        $url = "https://api.telegram.org/bot{$this->token}/{$method}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);

        $result = json_decode($response, true);

        if (!$result) {
            $result = [
                'ok' => false,
                'error' => 'Invalid JSON',
                'response' => $response,
            ];
        }
        curl_close($ch);
        return $result;
    }


}