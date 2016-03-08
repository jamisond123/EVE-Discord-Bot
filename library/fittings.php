<?php
/**
 * @return mixed|null
 */

function insertFit($db, $user, $pass, $dbName, $fitName, $fitAuthor, $fit)
{

    $conn = new mysqli($db, $user, $pass, $dbName);

    $sql = "INSERT INTO shipFits (fitName, fitAuthor, fit) VALUES ('$fitName', '$fitAuthor', '$fit')";

    if ($conn->query($sql) === TRUE) {
        return null;
    } else {
        return null;
    }
}