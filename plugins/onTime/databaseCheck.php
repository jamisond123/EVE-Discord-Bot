<?php

// Check for an updated database every 12 hours
$loop->addPeriodicTimer(43200, function() use ($logger, $client) {
    $logger->info("Checking for a new update for the CCP database");
    updateCCPData($logger);
});