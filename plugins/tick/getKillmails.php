<?php

/**
 * Class getKillmails
 */
class getKillmails
{
    /**
     * @var
     */
    var $config;
    /**
     * @var
     */
    var $db;
    /**
     * @var
     */
    var $discord;
    /**
     * @var
     */
    var $channelConfig;
    /**
     * @var int
     */
    var $lastCheck = 0;
    /**
     * @var
     */
    var $logger;
    public $newestKillmailID;
    public $kmChannel;
    public $corpID;
    public $startMail;

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
        $this->kmChannel = $config["plugins"]["getKillmails"]["channel"];
        $this->corpID = $config["plugins"]["getKillmails"]["corpID"];
        $this->startMail = $config["plugins"]["getKillmails"]["startMail"];
        if(2 > 1) // Schedule it for right now
            setPermCache("killmailCheck{$this->corpID}", time() - 5);
    }



    /**
     * @return array
     */
    function information()
    {
        return array(
            "name" => "",
            "trigger" => array(),
            "information" => ""
        );
    }

    /**
     *
     */
    function tick()
    {
        $lastChecked = getPermCache("killmailCheck{$this->corpID}");
        if ($lastChecked <= time()) {
            $this->logger->info("Checking for new killmails.");
            $oldID = getPermCache("newestKillmailID");
            $one = '1';
            $updatedID = $oldID + $one;
            setPermCache("newestKillmailID", $updatedID);
            $this->getKM();
            setPermCache("killmailCheck{$this->corpID}", time() + 600);
        }

    }

    function getKM()
    {
        $this->newestKillmailID = getPermCache("newestKillmailID");
        $lastMail = $this->newestKillmailID;
        $url = "https://zkillboard.com/api/xml/no-attackers/no-items/orderDirection/asc/afterKillID/{$lastMail}/corporationID/{$this->corpID}";
        $xml = simplexml_load_file($url);
        $kills = $xml->result->rowset->row;
        foreach ($kills as $kill) {
            $killID = $kill->attributes()->killID;
            $solarSystemID = $kill->attributes()->solarSystemID;
            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $solarSystemID), "ccp");
            $killTime = $kill->attributes()->killTime;
            $victimAllianceName = $kill->victim->attributes()->allianceName;
            $victimName = $kill->victim->attributes()->characterName;
            $victimCorpName = $kill->victim->attributes()->corporationName;
            $victimShipID = $kill->victim->attributes()->shipTypeID;
            $shipName = dbQueryField("SELECT typeName FROM invTypes WHERE typeID = :id", "typeName", array(":id" => $victimShipID), "ccp");
            // Check if it's a structure
            if ($victimName != "") {
                $msg = "**{$killTime}**\n\n**{$shipName}** flown by **{$victimName}** of (***{$victimCorpName}|{$victimAllianceName}***) killed in {$systemName}\nhttps://zkillboard.com/kill/{$killID}/";
            }
            elseif ($victimName == ""){
                $msg = "**{$killTime}**\n\n**{$shipName}** of (***{$victimCorpName}|{$victimAllianceName}***) killed in {$systemName}\nhttps://zkillboard.com/kill/{$killID}/";
            }
            $this->discord->api("channel")->messages()->create($this->kmChannel, $msg);
            setPermCache("newestKillmailID", $killID);

            sleep (5);
        }
        $updatedID = getPermCache("newestKillmailID");
        $this->logger->info("All kills posted, newest kill id is {$updatedID}");
        return null;
    }

    /**
     * @param $msgData
     */
    function onMessage($msgData)
    {
    }
}