<?php

/**
 * @param $url
 * @param $user
 * @param $pass
 * @param $dbName
 * @param $userID
 * @param $characterID
 * @param $eveName
 * @param $type
 * @return null
 */


function insertUser($url, $user, $pass, $dbName, $userID, $characterID, $eveName, $type){
    $host = $url;
    $username = $user;
    $password = $pass;
    $database = $dbName;
    $mysqli = mysqli_connect($host,$username,$password,$database);

    if ($stmt = $mysqli->prepare("REPLACE into authUsers (characterID, discordID, eveName, active, role) values(?,?,?,'yes',?)")) {

        // Bind the variables to the parameter as strings.
        $stmt->bind_param("ssss", $characterID,$userID,$eveName,$type);

        // Execute the statement.
        $stmt->execute();

        // Close the prepared statement.
        $stmt->close();
        return null;
    }
    return null;
}

/**
 * @param $url
 * @param $user
 * @param $pass
 * @param $dbName
 * @param $authCode
 * @return null
 */
function disableReg($url, $user, $pass, $dbName, $authCode){
    $host = $url;
    $username = $user;
    $password = $pass;
    $database = $dbName;
    $mysqli = mysqli_connect($host,$username,$password,$database);

    if ($stmt = $mysqli->prepare("UPDATE pendingUsers SET active='0' WHERE authString= ?")) {

        // Bind the variables to the parameter as strings.
        $stmt->bind_param("s", $authCode);

        // Execute the statement.
        $stmt->execute();

        // Close the prepared statement.
        $stmt->close();
        return null;
    }
    return null;
}

/**
 * @param $url
 * @param $user
 * @param $pass
 * @param $dbName
 * @param $authCode
 * @return bool|null
 */
function selectPending($url, $user, $pass, $dbName, $authCode){
    $host = $url;
    $username = $user;
    $password = $pass;
    $database = $dbName;
    $mysqli = mysqli_connect($host,$username,$password,$database);

    if ($stmt = $mysqli->prepare("SELECT * FROM pendingUsers WHERE authString= ? AND active='1'")) {

        // Bind the variables to the parameter as strings.
        $stmt->bind_param("s", $authCode);

        // Execute the statement.
        $result = $stmt->execute();

        // Close the prepared statement.
        $stmt->close();
        return $result;
    }
    return null;
}