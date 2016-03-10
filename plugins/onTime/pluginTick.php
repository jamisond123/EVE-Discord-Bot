<?php

// Plugin tick timer (1 second)
$loop->addPeriodicTimer(1, function () use ($logger, $client, $plugins) {
    foreach ($plugins as $plugin) {
        try {
            $plugin->tick();
        } catch (Exception $e) {
            $logger->warn("Error: " . $e->getMessage());
            }
    }
});