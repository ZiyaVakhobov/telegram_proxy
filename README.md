# Simple PHP based Telegram Proxy for Development and Testing
## You can run and test your telegram bot without webhook and using XDebug
`composer require ziya/telegram_proxy "^0.1"`
## Examples
```
<?php
    require __DIR__ . '/vendor/autoload.php';

    $bot_api_key = '<bot token>';
    $bot_username = '<bot usernane>';
    $url = '<url: ip address, domain name or your localhost>';
    $proxy = new \ziya\Proxy\ProxyLoop($bot_api_key, $bot_username, $url);
    $proxy->loop();
```