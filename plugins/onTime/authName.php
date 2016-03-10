<?php

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