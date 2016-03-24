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
 * Class corporationmails
 */
class evemails {
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
    var $nextCheck;
    /**
     * @var
     */
    var $toIDs;
    /**
     * @var
     */
    var $toDiscordChannel;

    /**
     * @var
     */
    var $newestMailID;
    /**
     * @var
     */
    var $maxID;
    /**
     * @var
     */
    var $keyCount;
    /**
     * @var
     */
    var $keys;

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
        $this->toIDs = $config["plugins"]["evemails"]["fromIDs"];
        $this->toDiscordChannel = $config["plugins"]["evemails"]["channelID"];
        $this->newestMailID = getPermCache("newestCorpMailID");
        $this->maxID = 0;
        $this->keyCount = count($config["eve"]["apiKeys"]);
        $this->keys = $config["eve"]["apiKeys"];
        $this->nextCheck = 0;

        // Schedule all the apiKeys for the future
        $keyCounter = 0;
        foreach ($this->keys as $keyOwner => $apiData) {
            $keyID = $apiData["keyID"];
            $characterID = $apiData["characterID"];

            if ($keyCounter == 0) {
                // Schedule it for right now
                setPermCache("corpMailCheck{$keyID}{$keyOwner}{$characterID}", time() - 5);
            }
            $keyCounter++;
        }
    }

    /**
     *
     */
    function tick()
    {
        $check = true;
        foreach ($this->keys as $keyOwner => $api) {
            if ($check == false) {
                continue;
            }

            $keyID = $api["keyID"];
            $vCode = $api["vCode"];
            $characterID = $api["characterID"];
            $lastChecked = getPermCache("corpMailCheck{$keyID}{$keyOwner}{$characterID}");

            if ($lastChecked <= time()) {
                $this->logger->info("Checking API Key {$keyID} belonging to {$keyOwner} for new corp mails");
                $this->checkMails($keyID, $vCode, $characterID);
                setPermCache("corpMailCheck{$keyID}{$keyOwner}{$characterID}", time() + 1807); // Reschedule it's check for 30minutes from now (plus 7 seconds, because CCP isn't known to adhere strictly to timeouts, lol)
                $check = false;
            }
        }
    }

    function checkMails($keyID, $vCode, $characterID)
    {
        $updateMaxID = false;
        $url = "https://api.eveonline.com/char/MailMessages.xml.aspx?keyID={$keyID}&vCode={$vCode}&characterID={$characterID}";
        $data = json_decode(json_encode(simplexml_load_string(downloadData($url), "SimpleXMLElement", LIBXML_NOCDATA)), true);
        $data = $data["result"]["rowset"]["row"];

        $mails = array();
        $mails[] = $data["@attributes"];
        // Sometimes there is only ONE notification, so.. yeah..
        if (count($data) > 1) {
            foreach ($data as $multiMail) {
                $mails[] = $multiMail["@attributes"];
            }
        }

        usort($mails, array($this, "sortByDate"));

        foreach ($mails as $mail) {
            if (in_array($mail["toCorpOrAllianceID"], $this->toIDs) && $mail["messageID"] > $this->newestMailID) {
                $sentBy = $mail["senderName"];
                $title = $mail["title"];
                $sentDate = $mail["sentDate"];
                $url = "https://api.eveonline.com/char/MailBodies.xml.aspx?keyID={$keyID}&vCode={$vCode}&characterID={$characterID}&ids=" . $mail["messageID"];
                $content = strip_tags(str_replace("<br>", "\n", json_decode(json_encode(simplexml_load_string(downloadData($url), "SimpleXMLElement", LIBXML_NOCDATA)))->result->rowset->row));

                // Blank Content Check
                if ($content == ""){
                    return null;
                }
                
                $messageSplit = str_split($content, 1850);

                // Stitch the mail together
                $msg = "**Mail By: **{$sentBy}\n";
                $msg .= "**Sent Date: **{$sentDate}\n";
                $msg .= "**Title: ** {$title}\n";
                $msg .= "**Content: **\n";
                $msg .= htmlspecialchars_decode(trim($messageSplit[0]));
                $msgLong = htmlspecialchars_decode(trim($messageSplit[1]));

                // Send the mails to the channel
                $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msg);
                sleep(1); // Lets sleep for a second, so we don't rage spam
                if (strlen($content) > 1850) {
                    $this->discord->api("channel")->messages()->create($this->toDiscordChannel, $msgLong);
                }

                // Find the maxID so we don't spit this message out ever again
                $this->maxID = max($mail["messageID"], $this->maxID);
                $this->newestMailID = $this->maxID; //$mail["messageID"];
                $updateMaxID = true;

                // set the maxID
                if ($updateMaxID) {
                    setPermCache("newestCorpMailID", $this->maxID);
                }
            }
        }
    }

    /**
     * @param $alpha
     * @param $bravo
     * @return int
     */
    function sortByDate($alpha, $bravo)
    {
        return strcmp($alpha["sentDate"], $bravo["sentDate"]);
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
