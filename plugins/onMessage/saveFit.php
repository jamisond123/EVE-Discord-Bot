<?php
class saveFit
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
    public $seatBase;

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
        $this->fitChannel = $config["plugins"]["saveFits"]["channel"];
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
        // Bind a few things to vars for the plugins
        $message = $msgData["message"]["message"];
        $channelID = $msgData["message"]["channelID"];
        $userName = $msgData["message"]["from"];
        $data = command($message, $this->information()["trigger"]);
        if (isset($data["trigger"])) {
            if ($channelID != $this->fitChannel){
                $this->discord->api("channel")->messages()->create($channelID, "Not allowed to add fits from this channel.");
                Return Null;
            }
            $field = explode(" ",  $data["messageString"], 2);
            $post = [
                'fitName' => $field[0],
                'fit' => $field[1],
            ];

            $fitName = str_replace("_"," ",$field[0]);
            $cleanApo = str_replace("'","''",$field[1]);

            $fit = addslashes($cleanApo);
            insertFit($this->db, $this->dbUser, $this->dbPass, $this->dbName, $fitName, $userName, $fit);

            $msg = $field[0] ." Successfully Submitted.";
            $this->logger->info("Fit submitted by ". $userName);
            $this->discord->api("channel")->messages()->create($channelID, $msg);
        }
        return null;
    }
    /**
     * @return array
     */
    function information()
    {
        return array(
            "name" => "savefit",
            "trigger" => array("!savefit"),
            "information" => "Use !savefit <fit_name> <eft_fit> to save a fit to be called using the !fit command. Please include underscores in place of spaces for the fit name. They will be removed later on."
        );
    }
    /**
     * @param $msgData
     */
    function onMessageAdmin($msgData)
    {
    }
}
