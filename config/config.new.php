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

$config = array();

$config["bot"] = array(
    "name" => "" // Discord name for your bot
);

$config["database"] = array(
    "host" => "", // Mysql db. Usually localhost
    "user" => "", // User
    "pass" => "", // Pass
    "database" => "" // DB, usually discord
);

$config["seat"] = array(
    "url" => "", // http://url.com/ (ONLY NEEDED FOR THE ADDAPI PLUGIN)
    "token" => "" // api token
);

$config["discord"] = array(
    "email" => "", // login email for the bot, make a separate account
    "password" => "", // login pass for the bot
    "admin" => "", // The owner of the bot
    "adminID" => "", // The discordID of the owner of the bot
    "guildID" => "152677265635803136" // The channelID of the corp/alliance
);

// Twitter
$config["twitter"] = array(
    "consumerKey" => "", // twitter integration, google for more info
    "consumerSecret" => "", // twitter integration, google for more info
    "accessToken" => "", // twitter integration, google for more info
    "accessTokenSecret" => "" // twitter integration, google for more info
);

$config["eve"] = array(
    "apiKeys" => array(
        "user1" => array(
            "keyID" => "", // User 1 is a must for notifications and mail to work, 2 users is preferred because of api key caching.
            "vCode" => "",
            "characterID" => ""
        ),
        "user2" => array(
            "keyID" => "",
            "vCode" => "",
            "characterID" => ""
        )
    )
);

$config["enabledPlugins"] = array(
    "about", //info on the bot
    "apiauth", //api based auth system
    "auth", //sso based auth system
    "charInfo", // eve character info using eve-kill
    //"corpApplication", //still a WIP
    "corpInfo", // eve corp info
    "eveStatus", // tq status
    "help", // bot help program, will list active addons
    "item", // item info, mostly useless info
    "price", // price check tool, works for all items and ships. Can either !pc <itemname> for general, or !<systemname> <item> for more specific
    "time", // global clock with eve time
    "user", // discord user info
    "wolframAlpha", // a "smart" tool. Ask you bot questions with !wolf <question>
    "evemails", // evemail updater, will post corp and alliance mails to a channel.
    //"fileReader", // Read advanced plugin config section of the wiki
    "notifications", // eve notifications to a channel, good for warning users of an attack
    "twitterOutput", // twitter input to stay up to date on eve happenings
    //"saveFit", // fitting tool, still a WIP
    //"showFit", // show fittings saved using the plugin above
    "getKillmails" // show corp killmails in a chat channel
);

// Example from the 4M server
$config["plugins"] = array(
    "periodicTQStatus" => array(
        "channelID" => 118441700157816838 // what channel do u want server status reported too
    ),
    "evemails" => array(
        "fromIDs" => array(98047305, 99005805), // fill in with id's you want info from (have to be accessible with the api)
        "channelID" => 120639051261804544 // what channel id like these to post too
    ),
    "fileReader" => array( // Documentation on this will be added to the wiki, used for pulling and posting jabber pings
        "db" => "/tmp/discord.db", // what file is read, DO NOT CHANGE.
        "channelConfig" => array(
            "pings" => array(
                "default" => true, // is this particular filter active?
                "searchString" => "broadcast", // The plugin will search for this string and post any messages that contain it. To have the bot share everything change it to false without any quotes.
                "textStringPrepend" => "@everyone |", // this prepend will ping all discord users with access to the channel
                "textStringAppend" => "", // anything ud like to add to the tail end of the bots message
                "channelID" => 119136919346085888 // channel it posts too
            ),
            "intel" => array(
                "default" => false,
                "searchString" => "intel",
                "textStringPrepend" => "",
                "textStringAppend" => "",
                "channelID" => 149918425018400768
            ),
            "blackops" => array(
                "default" => false,
                "searchString" => "blops",
                "textStringPrepend" => "@everyone |",
                "textStringAppend" => "",
                "channelID" => 149925578135306240
            )
        ),
    ),
    "notifications" => array(
        "channelID" => 149918425018400768 // what channel for eve notifications
    ),
    "twitterOutput" => array(
        "channelID" => 120474010109607937 // twitter output channel
    ),
    "wolframAlpha" => array(
        "appID" => "" // get an appID for wolframAlpha if you want to use the !wolf command
    ),
    "priceChecker" => array(
        "channelID" => "" //If you want to restrict price checker from working in a channel, put that channel's ID here.
    ),
    "auth" => array(
        "corpid" => "",
        "allianceid" => "0", // If you'd like to auth base on alliance put the alliance ID here.. also works to set blues.. DOES NOT WORK WITH API AUTH
        "guildID" => "", // The serverID for your discord server.
        "corpmemberRole" => "", // The name of the role your CORP members will be assigned too if the auth plugin is active.
        "allymemberRole" => "", // The name of the role your ALLY members will be assigned too if the auth plugin is active.
        "periodicCheck" => "false", // put "true" or "false", stating you either do or don't want the bot auto removing members who leave corp. If not using auth leave as "false".
        "alertChannel" => "", // if using periodic check put the channel you'd like the bot to log removing users in. (Recommended you don't use an active chat channel)
        "nameEnforce" => "false", // put "true" or "false", if you'd like to make sure people's name match character names
        "url" => "" // put a url here if using sso auth too your sso page.
    ),
    "saveFits" => array(
        "channel" => "" //Restrict saving fits to this channel. Use this to control who has access to saving fits. Use the channel ID.
    ),
    "getKillmails" => array(
        "channel" => "", //killmails post to this channel
        "corpID" => "", //corpid for killmails
        "allianceID" => "0", //allianceid for killmails (Leave as 0 if using it for a corp)
        "lossMails" => "true", //set as true to post both kills and losses, false to post only kills.
        "spamAmount" => "15", //Max amount of kills the bot will post every 10 minutes. Default is 15 and won't get the bot kicked for spamming.
    )
);
