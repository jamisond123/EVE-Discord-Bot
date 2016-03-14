<?php

/**
 * Class time
 */
class time
{
    /**
     * @var
     */
    var $config;
    /**
     * @var
     */
    var $discord;
    /**
     * @var
     */
    var $logger;

    /**
     * @param $config
     * @param $discord
     * @param $logger
     */
    function init($config, $discord, $logger)
    {
        $this->config = $config;
        $this->discord = $discord;
        $this->logger = $logger;
    }

    /**
     *
     */
    function tick()
    {

    }

    /**
     * @param $msgData
     */
    function onMessage($msgData)
    {
        $message = $msgData["message"]["message"];
        $channelName = $msgData["channel"]["name"];
        $guildName = $msgData["guild"]["name"];
        $channelID = $msgData["message"]["channelID"];

        $data = command($message, $this->information()["trigger"]);
        if (isset($data["trigger"])) {
            $date = date("d-m-Y");
            $fullDate = date("Y-m-d H:i:s");
            $datetime = new DateTime($fullDate);
            $est = $datetime->setTimezone(new DateTimeZone("America/New_York"));
            $est = $est->format("H:i:s");
            $pst = $datetime->setTimezone(new DateTimeZone("America/Los_Angeles"));
            $pst = $pst->format("H:i:s");
            $utc = $datetime->setTimezone(new DateTimeZone("UTC"));
            $utc = $utc->format("H:i:s");
            $cet = $datetime->setTimezone(new DateTimeZone("Europe/Copenhagen"));
            $cet = $cet->format("H:i:s");
            $msk = $datetime->setTimezone(new DateTimeZone("Europe/Moscow"));
            $msk = $msk->format("H:i:s");
            $aus = $datetime->setTimezone(new DateTimeZone("Australia/Sydney"));
            $aus = $aus->format("H:i:s");

            $this->logger->info("Sending time info to {$channelName} on {$guildName}");
            $this->discord->api("channel")->messages()->create($channelID, "**EVE Time:** {$utc} / **EVE Date:** {$date} / **PT:** {$pst} / **ET:** {$est} / **CET:** {$cet} / **MSK:** {$msk} / **AEST:** {$aus}");
        }
    }

    /**
     * @return array
     */
    function information()
    {
        return array(
            "name" => "time",
            "trigger" => array("!time"),
            "information" => "This shows the time for various timezones compared to EVE Time. To use simply type <!time>"
        );
    }
}