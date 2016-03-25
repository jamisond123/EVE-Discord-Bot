<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Robert Sardinia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * Class siloFull
 */
class siloFull {
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
     * @var
     */
    var $toDiscordChannel;
    protected $keyID;
    protected $vCode;
    protected $prefix;

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
        $this->toDiscordChannel = $config["plugins"]["siloFull"]["channelID"];
        $this->keyID = $config["plugins"]["siloFull"]["keyID"];
        $this->vCode = $config["plugins"]["siloFull"]["vCode"];
        if (2 > 1) {
            // Schedule it for right now
            setPermCache("siloLastChecked{$this->keyID}", time() - 5);
        }
    }

    /**
     *
     */
    function tick()
    {
        $lastChecked = getPermCache("siloLastChecked{$this->keyID}");
        $keyID = $this->keyID;
        $vCode = $this->vCode;

        if ($lastChecked <= time()) {
            $this->logger->info("Checking API Key {$keyID} for siphons");
            $this->checkTowers($keyID, $vCode);
            //6 hour +30 seconds, cache is for 6
            setPermCache("siloLastChecked{$keyID}", time() + 21660);
        }
    }

    function checkTowers($keyID, $vCode)
    {
        $url = "https://api.eveonline.com/corp/AssetList.xml.aspx?keyID={$keyID}&vCode={$vCode}";
        $xml = simplexml_load_file($url);

        foreach ($xml->result->rowset->row as $structures){
            //Check silos
            if ($structures->attributes()->typeID == 14343){
                foreach ($structures->rowset->row as $silo){
                    //Avoid reporting empty silos
                    if ($silo->attributes()->typeID= 16634 ) {
                        if ($silo->attributes()->quantity >= 270000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16643 ) {
                        if ($silo->attributes()->quantity >= 67000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16647 ) {
                        if ($silo->attributes()->quantity >= 32500) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16641 ) {
                        if ($silo->attributes()->quantity >= 40000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16640 ) {
                        if ($silo->attributes()->quantity >= 67000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16635 ) {
                        if ($silo->attributes()->quantity >= 270000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16648 ) {
                        if ($silo->attributes()->quantity >= 32500) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16633 ) {
                        if ($silo->attributes()->quantity >= 270000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16646 ) {
                        if ($silo->attributes()->quantity >= 32500) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16651 ) {
                        if ($silo->attributes()->quantity >= 27000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16650 ) {
                        if ($silo->attributes()->quantity >= 27000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16644 ) {
                        if ($silo->attributes()->quantity >= 27000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16652 ) {
                        if ($silo->attributes()->quantity >= 27000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16639 ) {
                        if ($silo->attributes()->quantity >= 70000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16636 ) {
                        if ($silo->attributes()->quantity >= 270000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16649 ) {
                        if ($silo->attributes()->quantity >= 37000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16653 ) {
                        if ($silo->attributes()->quantity >= 27000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16638 ) {
                        if ($silo->attributes()->quantity >= 70000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16637 ) {
                        if ($silo->attributes()->quantity >= 70000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    if ($silo->attributes()->typeID= 16642 ) {
                        if ($silo->attributes()->quantity >= 27000) {
                            $systemName = dbQueryField("SELECT solarSystemName FROM mapSolarSystems WHERE solarSystemID = :id", "solarSystemName", array(":id" => $structures->attributes()->locationID), "ccp");
                            $msg = "**Silo Nearing Capacity**\n";
                            $msg .= "**System: **{$systemName}\n";
                            // Send the mails to the channel
                            $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                        }
                    }
                    }
                }
            }return null;
        }
        
    }

    /**
     *
     */
    function onMessage()
    {
    }

    /**
     * @return array
     */
    function information()
    {
        return array(
            "name" => "",
            "trigger" => array(""),
            "information" => ""
        );
    }
}
