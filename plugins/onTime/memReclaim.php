<?php

// Memory reclamation (30 minutes)
$loop->addPeriodicTimer(1800, function () use ($logger, $client) {
    $logger->info("Memory in use: " . memory_get_usage() / 1024 / 1024 . "MB");
    gc_collect_cycles(); // Collect garbage
    $logger->info("Memory in use after garbage collection: " . memory_get_usage() / 1024 / 1024 . "MB");
});