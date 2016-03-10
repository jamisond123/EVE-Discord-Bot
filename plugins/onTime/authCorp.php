<?php

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