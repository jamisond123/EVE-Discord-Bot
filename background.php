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

// More memory allowance
ini_set("memory_limit", "1024M");

// Enable garbage collection
gc_enable();

// Just incase we get launched from somewhere else
chdir(__DIR__);

// When the bot started
$startTime = time();

// Require the vendor stuff
require_once(__DIR__ . "/vendor/autoload.php");

// Require the config
if (file_exists(__DIR__ . "/config/config.php")) {
    require_once(__DIR__ . "/config/config.php");
} else {
    throw new Exception("config.php not found (you might wanna start by editing and renaming config_new.php)");
}

// Init the discord library
$discord = new \Discord\Discord($config["discord"]["email"], $config["discord"]["password"]);
$token = $discord->token();
$gateway = $discord->api("gateway")->show()["url"] . "/"; // need to end in / for it to not whine about it.. *sigh*

// Load the plugins (Probably a prettier way to do this that i haven't thought up yet)
$pluginDirs = array(__DIR__ . "/plugins/tick/*.php");
$plugins = array();
foreach ($pluginDirs as $dir) {
    foreach (glob($dir) as $plugin) {
        // Only load the plugins we want to load, according to the config
        if (!in_array(str_replace(".php", "", basename($plugin)), $config["enabledPlugins"]))
            continue;

        require_once($plugin);
        $logger->info("Background Plugin Loading: " . str_replace(".php", "", basename($plugin)));
        $fileName = str_replace(".php", "", basename($plugin));
        $p = new $fileName();
        $p->init($config, $discord, $logger);
        $plugins[] = $p;
    }
}
// Number of plugins loaded
$logger->info("Loaded: " . count($plugins) . " background plugins");

// Load all the timers
foreach (glob("plugins/onTime/*.php") as $onTime)
{
    include $onTime;
}

//Initiate chat based bot
include 'inChat.php';
$logger->info("Starting chat bot.");


$loop->run();