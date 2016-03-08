<?php

class help
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
        $channelID = $msgData["message"]["channelID"];

        $data = command($message, $this->information()["trigger"]);
        if (isset($data["trigger"])) {
            global $plugins; // Need to have the plugins that are loaded available, yes it's ugly, whatever, better than shitting up the rest of the code :P
            $messageString = $data["messageString"];

            if (!$messageString) {
                // Show all modules available
                $commands = array();
                foreach ($plugins as $plugin) {
                    $info = $plugin->information();
                    if (!empty($info["name"]))
                        $commands[] = $info["name"];
                }

                $this->discord->api("channel")->messages()->create($channelID, "Here is a list of plugins available: **" . implode("** |  **", $commands) . "** If you'd like help with a specific plugin simply use the command !help <PluginName>");
            } else {
                foreach ($plugins as $plugin) {
                    if ($messageString == $plugin->information()["name"]) {
                        $this->discord->api("channel")->messages()->create($channelID, $plugin->information()["information"]);
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    function information()
    {
        return array(
            "name" => "help",
            "trigger" => array("!help"),
            "information" => "Shows help for a plugin, or all the plugins available. Example: **!help pc**"
        );
    }
}