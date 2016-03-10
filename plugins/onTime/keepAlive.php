<?php

// Keep alive timer (Default to 30 seconds heartbeat interval)
$loop->addPeriodicTimer(15, function () use ($logger, $client) {
    //$logger->info("Sending keepalive"); // schh
    $client->send(
        json_encode(
            array(
                "op" => 1,
                "d" => time())
            ,
            JSON_NUMERIC_CHECK
        )
    );
});