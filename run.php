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

// Require the vendor stuff
require_once(__DIR__ . "/vendor/autoload.php");

// Load the library files (Probably a prettier way to do this that i haven't thought up yet)
foreach (glob(__DIR__ . "/library/*.php") as $lib)
    require_once($lib);

// Setup the event loop and logger
$loop = \React\EventLoop\Factory::create();
$logger = new \Zend\Log\Logger();
$writer = new \Zend\Log\Writer\Stream("php://output");
$logger->addWriter($writer);

// Check that all the databases are created!
$databases = array("ccpData.sqlite", "sluggard.sqlite");
$databaseDir = __DIR__ . "/database";
if(!file_exists($databaseDir))
    mkdir($databaseDir);
foreach($databases as $db)
    if(!file_exists($databaseDir . "/" . $db))
        touch($databaseDir . "/" . $db);

// Create the sluggard.sqlite tables
$logger->info("Checking for the presence of the database tables");
updateSluggardDB($logger);
updateCCPData($logger);

// Initiate background tasks
include 'background.php';
$logger->info("Initiating background tasks");
?>