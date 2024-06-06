<?php
require '/app/vendor/autoload.php';
use MaxMind\Db\Reader;

$db = new mysqli('db', 'root', 'ChangeMe!!!', 'honeypot');

if ($db->query("SHOW TABLES LIKE 'logs'")->fetch_array() === null)  {
    $createStatment = file_get_contents(__DIR__ . '/createTable.sql');
    $db->query($createStatment);
}

$reader = new Reader(__DIR__ . '/geo/dbip-city-lite-2024-05.mmdb');

$handle = fopen(__DIR__ . '/logs/opencanary.log', "r");
$query = "REPLACE INTO logs (logs.utc_time, dst_host, dst_port, local_time, local_time_adjusted, local_version, password, remote_version, username, honeycred, logtype, node_id, src_host, src_port, continent, country_code, country, city, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if (file_exists(__DIR__ . '/lastLine.txt')) {
    $lastLine = file_get_contents(__DIR__ . '/lastLine.txt');
} else {
    $lastLine = 0;
}

if ($handle) {
    $currentLine = 0;
    while (($line = fgets($handle)) !== false) {
        $currentLine++;
        if ((int) $lastLine < $currentLine) {
            $data = json_decode($line, true);
            if (!empty($data['src_host'] && (int)$data['dst_port'] !== -1)) {
                $ip = $data['src_host'];
                $geoData = $reader->get($ip);
                $continent = $geoData['continent']['names']['de'];
                $countryCode = $geoData['country']['iso_code'];
                $countryName = $geoData['country']['names']['de'];
                $city = $geoData['city']['names']['en'];
                $latitude = $geoData['location']['latitude'];
                $longitude = $geoData['location']['longitude'];
                $db->execute_query(
                    $query,
                    [
                        $data['utc_time'],
                        $data['dst_host'],
                        (int)$data['dst_port'],
                        $data['local_time'],
                        $data['local_time_adjusted'],
                        $data['logdata']['LOCALVERSION'] ?? null,
                        $data['logdata']['PASSWORD'] ?? null,
                        $data['logdata']['REMOTEVERSION'] ?? null,
                        $data['logdata']['USERNAME'] ?? null,
                        isset($data['honeycred']) ? (int)$data['honeycred'] : 0,
                        $data['logtype'],
                        $data['node_id'],
                        $data['src_host'],
                        $data['src_port'],
                        $continent,
                        $countryName,
                        $countryCode,
                        $city,
                        $latitude,
                        $longitude,
                    ]
                );
            }
            echo "Verarbeitete Zeile Nr: $currentLine\r";
            file_put_contents(__DIR__ . '/lastLine.txt', $currentLine);
        }

    }

    // Schließe das File-Handle, wenn du fertig bist
    fclose($handle);
} else {
    // Fehlerbehandlung, falls das File nicht geöffnet werden kann
    echo "Das File konnte nicht geöffnet werden.";
}