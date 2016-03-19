<?php


/**
 * @param $db
 * @param $user
 * @param $pass
 * @param $dbName
 * @param $userID
 * @param $characterID
 * @param $eveName
 * @param $type
 * @return null
 */
function insertUser($db, $user, $pass, $dbName, $userID, $characterID, $eveName, $type)
{

    $conn = new mysqli($db, $user, $pass, $dbName);

    $sql = "REPLACE into authUsers (characterID, discordID, eveName, active, role) values('$characterID','$userID','$eveName', 'yes', '$type')";


    if ($conn->query($sql) === TRUE) {
        return null;
    } else {
        return null;
    }
}

function disableReg($db, $user, $pass, $dbName, $authCode)
{

    $conn = new mysqli($db, $user, $pass, $dbName);

    $sql = "UPDATE pendingUsers SET active='0' WHERE authString='$authCode'";

    if ($conn->query($sql) === TRUE) {
        return null;
    } else {
        return null;
    }
}