<?php
/**
 * @param $url
 * @return mixed|null
 */
function makeApiRequest($url)
{
    // Initialize a new request for this URL
    $ch = curl_init($url);
    // Set the options for this request
    curl_setopt_array($ch, array(
        CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
        CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
        CURLOPT_SSL_VERIFYPEER => false, // Do not verify the SSL certificate
    ));
    // Fetch the data from the URL
    $data = curl_exec($ch);
    // Close the connection
    curl_close($ch);
    // Return a new SimpleXMLElement based upon the received data
    return new SimpleXMLElement($data);
}

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

    $sql = "INSERT INTO authUsers (characterID, discordID, eveName, role) VALUES ('$characterID','$userID','$eveName','$type')";

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