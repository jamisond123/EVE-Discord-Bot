<?php

// Startup the websocket connection
$client = new \Devristo\Phpws\Client\WebSocket($gateway, $loop, $logger);

// Setup the connection handlers
$client->on("connect", function() use ($logger, $client, $token) {
    $logger->notice("Re-connected!");
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

$client->on("message", function($message) use ($client, $logger, $discord, $plugins, $config) {
    // Decode the data
    $data = json_decode($message->getData());

    switch ($data->t) {
        case "READY":
            $logger->notice("Got READY frame");
            $logger->notice("Heartbeat interval: " . $data->d->heartbeat_interval / 1000.0 . " seconds");
            // Can't really use the heartbeat interval for anything, since i can't retroactively change the periodic timers.. but it's usually ~40ish seconds
            //$heartbeatInterval = $data->d->heartbeat_interval / 1000.0;
            break;

        case "MESSAGE_CREATE":
            // Map the data to $data, we don't need all the opcodes and whatnots here
            $data = $data->d;

            // Skip if it's the bot itself that wrote something
            if ($data->author->username == $config["bot"]["name"]) {
                continue;
            }

            // Create the data array for the plugins to use
            $channelData = $discord->api("channel")->show($data->channel_id);
            if ($channelData["is_private"]) {
                $channelData["name"] = $channelData["recipient"]["username"];
            }

            $msgData = array(
                "isBotOwner" => $data->author->username == $config["discord"]["admin"] || $data->author->id == $config["discord"]["adminID"] ? true : false,
                "message" => array(
                    "lastSeen" => dbQueryField("SELECT lastSeen FROM usersSeen WHERE id = :id", "lastSeen", array(":id" => $data->author->id)),
                    "lastSpoke" => dbQueryField("SELECT lastSpoke FROM usersSeen WHERE id = :id", "lastSpoke", array(":id" => $data->author->id)),
                    "timestamp" => $data->timestamp,
                    "id" => $data->id,
                    "message" => $data->content,
                    "channelID" => $data->channel_id,
                    "from" => $data->author->username,
                    "fromID" => $data->author->id,
                    "fromDiscriminator" => $data->author->discriminator,
                    "fromAvatar" => $data->author->avatar
                ),
                "channel" => $channelData,
                "guild" => $channelData["is_private"] ? array("name" => "private conversation") : $discord->api("guild")->show($channelData["guild_id"])
            );

            // Update the users status
            if ($data->author->id) {
                dbExecute("REPLACE INTO usersSeen (id, name, lastSeen, lastSpoke, lastWritten) VALUES (:id, :name, :lastSeen, :lastSpoke, :lastWritten)", array(":id" => $data->author->id, ":lastSeen" => date("Y-m-d H:i:s"), ":name" => $data->author->username, ":lastSpoke" => date("Y-m-d H:i:s"), ":lastWritten" => $data->content));
            }

            // Run the plugins
            foreach ($plugins as $plugin) {
                try {
                    $plugin->onMessage($msgData);
                } catch (Exception $e) {
                    $logger->warn("Error: " . $e->getMessage());
                }
            }
            break;

        case "TYPING_START": // When a person starts typing
        case "VOICE_STATE_UPDATE": // When someone switches voice channel (should be used for the sound part i guess?)
        case "CHANNEL_UPDATE": // When a channel gets update
        case "GUILD_UPDATE": // When the guild (server) gets updated
        case "GUILD_ROLE_UPDATE": // a role was updated in the guild
        case "MESSAGE_UPDATE": // a message gets updated, ignore it for now
        case "GUILD_MEMBER_UPDATE": // ignore
        case "GUILD_MEMBER_ADD": // ignore
        case "GUILD_MEMBER_REMOVE": // ignore
        case "MESSAGE_DELETE": // ignore
            //$logger->info("Ignoring: " . $data->t);
            // Ignore them
            break;

        case "PRESENCE_UPDATE": // Update a users status
            if ($data->d->user->id) {
                $id = $data->d->user->id;
                $lastSeen = date("Y-m-d H:i:s");
                $lastStatus = $data->d->status;
                $name = $discord->api("user")->show($id)["username"];
                dbExecute("REPLACE INTO usersSeen (id, name, lastSeen, lastStatus) VALUES (:id, :name, :lastSeen, :lastStatus)", array(":id" => $id, ":lastSeen" => $lastSeen, ":name" => $name, ":lastStatus" => $lastStatus));
            }
            break;

        default:
            $logger->err("Unknown case: " . $data->t);
            break;
    }
});

$client->open()->then(function() use ($logger, $client) {
    $logger->notice("Connection re-opened");
});

$loop->run();