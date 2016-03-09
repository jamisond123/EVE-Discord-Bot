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

// Check for an updated database every 12 hours
$loop->addPeriodicTimer(43200, function() use ($logger, $client) {
    $logger->info("Checking for a new update for the CCP database");
    updateCCPData($logger);
});

// Keep alive timer (Default to 30 seconds heartbeat interval)
$loop->addPeriodicTimer(30, function () use ($logger, $client) {
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

// Memory reclamation (30 minutes)
$loop->addPeriodicTimer(1800, function () use ($logger, $client) {
    $logger->info("Memory in use: " . memory_get_usage() / 1024 / 1024 . "MB");
    gc_collect_cycles(); // Collect garbage
    $logger->info("Memory in use after garbage collection: " . memory_get_usage() / 1024 / 1024 . "MB");
});

// Auth Corp Check
$loop->addPeriodicTimer(21600, function() use ($logger, $client, $discord, $config) {
    if ($config["plugins"]["auth"]["periodicCheck"] == "true") {
        $logger->info("Initiating Auth Check");
        $db = $config["database"]["host"];
        $dbUser = $config["database"]["user"];
        $dbPass = $config["database"]["pass"];
        $dbName = $config["database"]["database"];
        $corpID = $config["plugins"]["auth"]["corpid"];
        $guildID = $config["plugins"]["auth"]["guildID"];
        $toDiscordChannel = $config["plugins"]["auth"]["alertChannel"];
        $conn = new mysqli($db, $dbUser, $dbPass, $dbName);

        $sql = "SELECT characterID, discordID FROM authUsers WHERE role = 'corp'";

        $result = $conn->query($sql);
        $num_rows = $result->num_rows;

        if ($num_rows >= 1) {
            while ($rows = $result->fetch_assoc()) {
                $charid = $rows['characterID'];
                $discordid = $rows['discordID'];
                $url = "https://api.eveonline.com/eve/CharacterAffiliation.xml.aspx?ids=$charid";
                $xml = makeApiRequest($url);
                if ($xml->result->rowset->row[0]) {
                    foreach ($xml->result->rowset->row as $character) {
                        if ($character->attributes()->corporationID != $corpID) {
                            $discord->api("guild")->members()->redeploy($guildID, $discordid, "");
                            $discord->api("channel")->messages()->create($toDiscordChannel, "Discord user #" . $discordid . " corp roles removed via auth.");
                            $logger->info("Removing user " . $discordid);

                            $sql2 = "UPDATE authUsers SET active='no' WHERE discordID='$discordid'";
                            $result2 = $conn->query($sql2);
                        }
                    }
                }
            }
            $logger->info("All corp users successfully authed.");
            return null;

        }
        $logger->info("No corp users found in database.");
        return null;
    }
});
// Auth Alliance Check
$loop->addPeriodicTimer(21600, function() use ($logger, $client, $discord, $config) {
    if ($config["plugins"]["auth"]["periodicCheck"] == "true") {
        $logger->info("Initiating Auth Check");
        $db = $config["database"]["host"];
        $dbUser = $config["database"]["user"];
        $dbPass = $config["database"]["pass"];
        $dbName = $config["database"]["database"];
        $allyID = $config["plugins"]["auth"]["allianceid"];
        $guildID = $config["plugins"]["auth"]["guildID"];
        $toDiscordChannel = $config["plugins"]["auth"]["alertChannel"];
        $conn = new mysqli($db, $dbUser, $dbPass, $dbName);

        $sql3 = "SELECT characterID, discordID FROM authUsers WHERE role = 'ally'";

        $result3 = $conn->query($sql3);
        $num_rows2 = $result3->num_rows;

        if ($num_rows2>=1){
            while($rows = $result3->fetch_assoc()){
                $charid = $rows['characterID'];
                $discordid = $rows['discordID'];
                $url = "https://api.eveonline.com/eve/CharacterAffiliation.xml.aspx?ids=$charid";
                $xml = makeApiRequest($url);
                if ($xml->result->rowset->row[0]) {
                    foreach ($xml->result->rowset->row as $character) {
                        if ($character->attributes()->allianceID != $allyID) {
                            $discord->api("guild")->members()->redeploy($guildID, $discordid, "");
                            $discord->api("channel")->messages()->create($toDiscordChannel, "Discord user #" . $discordid ." alliance roles removed via auth.");
                            $logger->info("Removing user ". $discordid);

                            $sql4 = "UPDATE authUsers SET active='no' WHERE discordID='$discordid'";
                            $result4 = $conn->query($sql4);
                        }
                    }
                }
            }
            $logger->info("All alliance users successfully authed.");
            return null;

        }
        $logger->info("No alliance users found in database.");
        return null;


    }
    return null;
});

// Auth Name Check
$loop->addPeriodicTimer(1800, function() use ($logger, $client, $discord, $config) {
    if ($config["plugins"]["auth"]["nameEnforce"] == "true") {
        $logger->info("Initiating Name Check");
        $db = $config["database"]["host"];
        $dbUser = $config["database"]["user"];
        $dbPass = $config["database"]["pass"];
        $dbName = $config["database"]["database"];
        $guildID = $config["plugins"]["auth"]["guildID"];
        $toDiscordChannel = $config["plugins"]["auth"]["alertChannel"];
        $conn = new mysqli($db, $dbUser, $dbPass, $dbName);

        $sql = "SELECT characterID, discordID, eveName FROM authUsers";

        $result = $conn->query($sql);
        $num_rows = $result->num_rows;

        if ($num_rows>=1){
            while($rows = $result->fetch_assoc()){
                $discordid = $rows['discordID'];
                $eveName = $rows['eveName'];
                $userData = $discord->api('user')->show($discordid);
                $discordname = $userData['username'];
                if ($discordname != $eveName) {
                    $discord->api("guild")->members()->redeploy($guildID, $discordid, "");
                    $discord->api("channel")->messages()->create($toDiscordChannel, "Discord user " . $discordname ." roles removed via auth because discord name is not set as the user in-game name " . $eveName);
                    $logger->info("Removing user due to name being incorrect ". $discordid);

                    $sql2 = "UPDATE authUsers SET active='no' WHERE discordID='$discordid'";
                    $result2 = $conn->query($sql2);
                }



            }
            $logger->info("All users names are correct.");
            return null;

        }
        $logger->info("No users found in database.");
        return null;


    }
    return null;
});
