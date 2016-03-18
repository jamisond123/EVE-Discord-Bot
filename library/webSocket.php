<?php

$client = new \Devristo\Phpws\Client\WebSocket($gateway, $loop, $logger);

// Setup the connection handlers
$client->on("connect", function() use ($logger, $client, $token) {
    $logger->notice("Connected!");
    $client->send(
        json_encode(
            array(
                "op" => 2,
                "d" => array(
                    "token" => $token,
                    "properties" => array(
                        "\$os" => "linux",
                        "\$browser" => "discord.php",
                        "\$device" => "discord.php",
                        "\$referrer" => "",
                        "\$referring_domain" => ""
                    ),
                    "v" => 3)
            ),
            JSON_NUMERIC_CHECK
        )
    );
});

$client->open()->then(function() use ($logger, $client) {
    $logger->notice("Connection opened");
});