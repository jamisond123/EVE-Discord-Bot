<?php
class addapi
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
        $this->seatToken = $config["seat"]["token"];
        $this->seatBase = $config["seat"]["url"];
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
         $data = command($message, $this->information()["trigger"]);
         if (isset($data["trigger"])) {
             $field = explode(" ",  $data["messageString"]);
             $post = [
             'key_id' => $field[0],
             'v_code' => $field[1],
             ];

             // Basic check on entry validity. Need to make this more robust.
             if (strlen($field[0]) <> 7)
                 return $this->discord->api("channel")->messages()->create($channelID, "**Invalid KeyID**");
             if (strlen($field[1]) <> 64)
                 return $this->discord->api("channel")->messages()->create($channelID, "**Invalid vCode**");

             $url = $this->seatBase.'api/v1/key';

             seatPost($url, $post, $this->seatToken);

             $msg = "API Successfully Submitted.";
             $this->logger->info("API key submitted");
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
            "name" => "addapi",
            "trigger" => array("!addapi"),
            "information" => "To add an API private message this bot in the following format. !addapi KeyID vCode"
        );
    }
    /**
     * @param $msgData
     */
    function onMessageAdmin($msgData)
    {
    }
}
