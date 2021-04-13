<?php

namespace ziya\Proxy;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class ProxyLoop
{
    private $bot_token;
    private $bot_username;
    private $proxy_url;

    public function __construct($bot_token, $bot_username, $proxy_url)
    {
        $this->bot_token = $bot_token;
        $this->bot_username = $bot_username;
        $this->proxy_url = $proxy_url;
    }

    /**
     * @throws TelegramException
     */
    public function loop($sleep_time = 0.5, $verbose = false, $xdebug_enabled = false)
    {
        if ($debug_enabled) {
            $this->proxy_url .= '?XDEBUG_SESSION_START=1';
        }
        try {
            // Create Telegram API object
            $telegram = new Telegram($this->bot_token, $this->bot_username);
            $telegram->useGetUpdatesWithoutDatabase();
            while (true) {
                $response = $telegram->handleGetUpdates();
                foreach ($response->getRawData()['result'] as $item) {
                    $data = $item;
                    $options = array(
                        'http' => array(
                            'method' => 'POST',
                            'content' => json_encode($data),
                            'header' => "Content-Type: application/json\r\n" .
                                "Accept: application/json\r\n"
                        )
                    );
                    $context = stream_context_create($options);
                    $result = file_get_contents($this->proxy_url, false, $context);
                    if ($verbose) {
                        $response = json_decode($result);
                        print_r($response);
                    }
                    if ($sleep_time > 0) {
                        sleep($sleep_time);
                    }
                }
            }
        } catch (TelegramException $e) {
            throw $e;
        }
    }
}