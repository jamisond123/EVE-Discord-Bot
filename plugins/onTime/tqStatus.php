<?php

// Check if TQs status has changed
$loop->addPeriodicTimer(60, function() use ($logger, $client, $discord, $config) {
    $crestData = json_decode(downloadData("https://public-crest.eveonline.com/"), true);
    $tqStatus = isset($crestData["serviceStatus"]["eve"]) ? $crestData["serviceStatus"]["eve"] : "offline";
    $tqOnline = (int) $crestData["userCounts"]["eve"];

    // Store the current status in the permanent cache
    $oldStatus = getPermCache("eveTQStatus");
    if($tqStatus !== $oldStatus) {
        $msg = "**New TQ Status:** ***{$tqStatus}*** / ***{$tqOnline}*** users online.";
        $logger->info("TQ Status changed from {$oldStatus} to {$tqStatus}");
        $discord->api("channel")->messages()->create($config["plugins"]["periodicTQStatus"]["channelID"], $msg);
    }
    setPermCache("eveTQStatus", $tqStatus);
    return null;
});