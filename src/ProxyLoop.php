<?php

namespace ziya\Proxy;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class ProxyLoop
{
    private $bot_token;
    private $proxy_url;

    public function __construct($bot_token, $proxy_url)
    {
        $this->bot_token = $bot_token;
        $this->proxy_url = $proxy_url;
    }

    /**
     * @throws \Exception
     */
    public function loop($sleep_time = 0.5, $verbose = false, $xdebug_enabled = false)
    {
        if ($xdebug_enabled) {
            $this->proxy_url .= '?XDEBUG_SESSION_START=1';
        }
        try {
            // Create Telegram API object
            $telegram = new TelegramApi($this->bot_token);
            while (true) {
                $response = $telegram->getUpdates();
                foreach ($response['result'] ?? [] as $item) {
                    try {
                        $client_response = $this->request($this->proxy_url, $item);
                        if ($verbose) {
                            print_r($client_response);
                            echo "\n";
                        }
                        $telegram->setLastUpdate($item['update_id']);
                    } catch (\Exception $e) {
                        echo $e->getMessage() . PHP_EOL;
                    }
                    if ($sleep_time > 0) {
                        sleep($sleep_time);
                    }
                }
                usleep(45);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $url
     * @param $body
     * @param string $method
     * @param array $headers
     * @return bool|string
     */
    private function request($url, $body)
    {

        $ch = curl_init($url);

        $payload = json_encode($body);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}