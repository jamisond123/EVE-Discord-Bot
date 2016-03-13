<?php

class wolframAlpha
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
    var $wolf;

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
        require_once(__DIR__ . "/../../library/wolframAlpha/WolframAlphaEngine.php");
        $this->wolf = new WolframAlphaEngine($config["plugins"]["wolframAlpha"]["appID"]);
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
            $messageString = $data["messageString"];

            $response = $this->wolf->getResults($messageString);

            // There was an error
            if ($response->isError())
                var_dump($response->error);

            $guess = $response->getPods();
            if (isset($guess[1])) {
                $guess = $guess[1]->getSubpods();
                $text = $guess[0]->plaintext;
                $image = $guess[0]->image->attributes["src"];

                if (stristr($text, "\n"))
                    $text = str_replace("\n", " | ", $text);

                if (!empty($text))
                    $this->discord->api("channel")->messages()->create($channelID, $text);
                if (!empty($image))
                    $this->discord->api("channel")->messages()->create($channelID, $image);
            }
        }
    }

    /**
     * @return array
     */
    function information()
    {
        return array(
            "name" => "wolf",
            "trigger" => array("!wolf"),
            "information" => "Ask various questions and get interesting answers. Example being !wolf how many world series has the yankees won?"
        );
    }

        /**
         * @param $msgData
         */
        function onMessageAdmin($msgData)
        {
        }

}
