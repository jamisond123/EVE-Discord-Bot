<?php

/**
 * Class showFit
 */
class showFit
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
        $this->db = $config["database"]["host"];
        $this->dbUser = $config["database"]["user"];
        $this->dbPass = $config["database"]["pass"];
        $this->dbName = $config["database"]["database"];
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
        $channelID = $msgData["message"]["channelID"];

        $data = command($message, $this->information()["trigger"]);
        if (isset($data["trigger"])) {
            $fitChoice = stristr($data["messageString"], "@") ? str_replace("<@", "", str_replace(">", "", $data["messageString"])) : $data["messageString"];

            $conn = new mysqli($this->db, $this->dbUser, $this->dbPass, $this->dbName);
            $result = mysqli_query($conn, "SELECT * FROM shipFits WHERE fitName='$fitChoice'");
            while ($rows = $result->fetch_assoc()) {
                $fit = $rows['fit'];
                $cleanFit = str_replace("''","'",$fit);
                $fitAuthor = $rows['fitAuthor'];
                $message = "``` Fit Submitted By: {$fitAuthor}\n\n{$cleanFit}```";
                $this->discord->api("channel")->messages()->create($channelID, $message);
            }
        }
    }

    /**
     * @return array
     */
    function information()
    {
        return array(
            "name" => "fit",
            "trigger" => array("!fit"),
            "information" => "Show a saved fitting. !fit <fit_name>"
        );
    }
}