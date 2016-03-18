<?php
/**
 * @return mixed|null
 */

function serverStatus(){
    // Initialize a new request for this URL
    $ch = curl_init("https://api.eveonline.com/server/ServerStatus.xml.aspx");
    // Set the options for this request
    curl_setopt_array($ch, array(
        CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
        CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false, // Do not verify the SSL certificate
    ));
    // Fetch the data from the URL
    $data = curl_exec($ch);
    // Close the connection
    curl_close($ch);

    $true = "true";
    //If server is down return false
    if ($data->serverOpen != "True") {
        return FALSE;
    }
    //If server is up return true
    return $true;
}