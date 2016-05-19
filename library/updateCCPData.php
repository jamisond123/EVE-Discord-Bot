<?php

function updateCCPData($logger) {
    $databaseDir = __DIR__ . "/../database/";

    if(1 !== 2) {
        try {
            $logger->notice("Updating CCP SQLite DB");
            $logger->notice("Opening bz2 file");
            $sqliteData = bzopen("{$databaseDir}sqlite-latest.sqlite.bz2", "r");

            $logger->notice("Reading from bz2 file");
            $data = "";
            while(!feof($sqliteData))
                $data .= bzread($sqliteData, 4096);

            $logger->notice("Writing bz2 file contents into .sqlite file");
            file_put_contents("{$databaseDir}/ccpData.sqlite", $data);

            $logger->notice("Flushing bz2 data from memory");
            $data = null;
            $logger->notice("Memory in use: " . memory_get_usage() / 1024 / 1024 . "MB");
            gc_collect_cycles(); // Collect garbage
            $logger->notice("Memory in use after garbage collection: " . memory_get_usage() / 1024 / 1024 . "MB");

            $logger->notice("Deleting bz2 file");
            unlink("{$databaseDir}/sqlite-latest.sqlite.bz2");

            setPermCache("SluggardCCPDataMD5", $md5);

            // Create the mapCelestialsView
            $logger->notice("Creating the mapAllCelestials view");
            dbExecute("CREATE VIEW mapAllCelestials AS SELECT itemID, itemName, typeName, mapDenormalize.typeID, solarSystemName, mapDenormalize.solarSystemID, mapDenormalize.constellationID, mapDenormalize.regionID, mapRegions.regionName, orbitID, mapDenormalize.x, mapDenormalize.y, mapDenormalize.z FROM mapDenormalize JOIN invTypes ON (mapDenormalize.typeID = invTypes.typeID) JOIN mapSolarSystems ON (mapSolarSystems.solarSystemID = mapDenormalize.solarSystemID) JOIN mapRegions ON (mapDenormalize.regionID = mapRegions.regionID) JOIN mapConstellations ON (mapDenormalize.constellationID = mapConstellations.constellationID)", array(), "ccp");
			$logger->notice("mapAllCelestials view created");
			$logger->notice("Database Update Complete");
			
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
