<?php

// Auth Alliance Check
$loop->addPeriodicTimer(21600, function() use ($logger, $client, $discord, $config) {
if ($config["plugins"]["auth"]["periodicCheck"] == "true") {
$logger->info("Initiating Auth Check");
$db = $config["database"]["host"];
$dbUser = $config["database"]["user"];
$dbPass = $config["database"]["pass"];
$dbName = $config["database"]["database"];
$allyID = $config["plugins"]["auth"]["allianceid"];
$guildID = $config["plugins"]["auth"]["guildID"];
$toDiscordChannel = $config["plugins"]["auth"]["alertChannel"];
$conn = new mysqli($db, $dbUser, $dbPass, $dbName);

$sql3 = "SELECT characterID, discordID FROM authUsers WHERE role = 'ally'";

$result3 = $conn->query($sql3);
$num_rows2 = $result3->num_rows;

if ($num_rows2>=1){
while($rows = $result3->fetch_assoc()){
$charid = $rows['characterID'];
$discordid = $rows['discordID'];
$url = "https://api.eveonline.com/eve/CharacterAffiliation.xml.aspx?ids=$charid";
$xml = makeApiRequest($url);
if ($xml->result->rowset->row[0]) {
foreach ($xml->result->rowset->row as $character) {
if ($character->attributes()->allianceID != $allyID) {
$discord->api("guild")->members()->redeploy($guildID, $discordid, "");
$discord->api("channel")->messages()->create($toDiscordChannel, "Discord user #" . $discordid ." alliance roles removed via auth.");
$logger->info("Removing user ". $discordid);

$sql4 = "UPDATE authUsers SET active='no' WHERE discordID='$discordid'";
$result4 = $conn->query($sql4);
}
}
}
}
$logger->info("All alliance users successfully authed.");
return null;

}
$logger->info("No alliance users found in database.");
return null;


}
return null;
});