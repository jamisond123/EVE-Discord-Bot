<?php

/**
 * Class auth
 */
class auth
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
    var $solarSystems;
    var $triggers = array();
    public $guildID;
    public $roleName;
    public $corpID;
    public $db;
    public $dbUser;
    public $dbPass;
    public $dbName;
    public $forceName;
    public $ssoUrl;
    public $nameEnforce;

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
        $this->corpID = $config["plugins"]["auth"]["corpid"];
        $this->allianceID = $config["plugins"]["auth"]["allianceid"];
        $this->guildID = $config["plugins"]["auth"]["guildID"];
        $this->roleName = $config["plugins"]["auth"]["corpmemberRole"];
        $this->allyroleName = $config["plugins"]["auth"]["allymemberRole"];
        $this->nameEnforce = $config["plugins"]["auth"]["nameEnforce"];
        $this->ssoUrl = $config["plugins"]["auth"]["url"];
    }
    /**
     *
     */
    function tick()
    {
    }
    /**
     * @param $msgData
     * @return null
     */
    function onMessage($msgData)
    {
        $userID = @$msgData["channel"]["recipient"]["id"];
        $userName = $msgData["message"]["from"];
        $message = $msgData["message"]["message"];
        $channelID = $msgData["message"]["channelID"];
        $data = command($message, $this->information()["trigger"]);
        if (isset($data["trigger"])) {
            $code = $data["messageString"];
            $conn = new mysqli($this->db, $this->dbUser, $this->dbPass, $this->dbName);
            $result = mysqli_query($conn,"SELECT * FROM pendingUsers WHERE authString='$code' AND active='1'");
            while($rows = $result->fetch_assoc()){
                $charid = $rows['characterID'];
                $corpid = $rows['corporationID'];
                $allianceid = $rows['allianceID'];
                $url = "https://api.eveonline.com/eve/CharacterName.xml.aspx?ids=$charid";
                $xml = makeApiRequest($url);

                // We have an error, show it it
                if ($xml->error) {
                    $this->discord->api("channel")->messages()->create($channelID, "**Failure:** Eve API is down, please try again in a little while.");
                    return null;
                }
                elseif ($this->nameEnforce == 'true') {
                    foreach($xml->result->rowset->row as $character){
                        if ($character->attributes()->name != $userName) {
                            $this->discord->api("channel")->messages()->create($channelID, "**Failure:** Your discord name must match your character name.");
                            $this->logger->info("User was denied due to not having the correct name ". $character->attributes()->name);
                            return null;

                        }
                    }
                }
                $url = "https://api.eveonline.com/eve/CharacterName.xml.aspx?ids=$charid";
                $xml = makeApiRequest($url);
                foreach($xml->result->rowset->row as $character){
                    $eveName = $character->attributes()->name;
                    if ($corpid == $this->corpID){
                        $guildData = $this->discord->api("guild")->show($this->guildID);
                        foreach($guildData["roles"] as $role) {
                            $roleID = $role["id"];
                            if ($role["name"] == $this->roleName) {
                                $this->discord->api("guild")->members()->redeploy($this->guildID, $userID, array($roleID));
                                insertUser($this->db, $this->dbUser, $this->dbPass, $this->dbName, $userID, $charid, $eveName, 'corp');
                                disableReg($this->db, $this->dbUser, $this->dbPass, $this->dbName, $code);
                                $this->discord->api("channel")->messages()->create($channelID, "**Success:** You have now been added to the " . $this->roleName . " group. To get more roles, talk to the CEO / Directors");
                                $this->logger->info("User authed and added to corp group ". $eveName);
                                return null;
                            }
                        }
                    }
                }
                foreach($xml->result->rowset->row as $character){
                    $eveName = $character->attributes()->name;
                    if ($allianceid == $this->allianceID){
                        $guildData = $this->discord->api("guild")->show($this->guildID);
                        foreach($guildData["roles"] as $role) {
                            $roleID = $role["id"];
                            if ($role["name"] == $this->allyroleName) {
                                $this->discord->api("guild")->members()->redeploy($this->guildID, $userID, array($roleID));
                                insertUser($this->db, $this->dbUser, $this->dbPass, $this->dbName, $userID, $charid, $eveName, 'ally');
                                disableReg($this->db, $this->dbUser, $this->dbPass, $this->dbName, $code);
                                $this->discord->api("channel")->messages()->create($channelID, "**Success:** You have now been added to the " . $this->allyroleName . " group. To get more roles, talk to the CEO / Directors");
                                $this->logger->info("User authed and added to alliance group ". $discordid);
                                return null;
                            }
                        }
                    }
                }

            }
            $this->discord->api("channel")->messages()->create($channelID, "**Failure:** No roles available for your corp or alliance.");
            $this->logger->info("User was denied due to not being in the correct corp or alliance ". $discordid);
            return null;
        }
        return null;
    }
    /**
     * @return array
     */
    function information()
    {
        return array(
            "name" => "auth",
            "trigger" => array("!auth"),
            "information" => "SSO based auth system. ". $this->ssoUrl ." be sure you ***private message the bot when performing this command.***"
        );
    }
    /**
     * @param $msgData
     */
    function onMessageAdmin($msgData)
    {
    }
}