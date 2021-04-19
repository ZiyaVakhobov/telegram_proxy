<?php

namespace ziya\Proxy;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
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
        if ($xdebug_enabled) {
            $this->proxy_url .= '?XDEBUG_SESSION_START=1';
        }
        try {
            // Create Telegram API object
            $telegram = new Telegram($this->bot_token, $this->bot_username);
            $telegram->useGetUpdatesWithoutDatabase();
            while (true) {
                $response = $telegram->handleGetUpdates();
                foreach ($response->getResult() as $item) {
                    $data = $item;
                    $client = new Client(['base_uri' => $this->proxy_url]);
                    try {
                        $client_response = $client->request('POST', '', [
                            RequestOptions::JSON => $data
                        ]);
                        if ($verbose) {
                            print_r($client_response);
                        }
                    } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                        echo $e->getResponse()->getBody()->getContents() . PHP_EOL;
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