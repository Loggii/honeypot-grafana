<?php

//$db = new SQLite3(__DIR__ . '/grafana-storage/import/logs.db', SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
//$db->exec("CREATE TABLE IF NOT EXISTS network_logs (
//    dst_host TEXT NOT NULL,
//    dst_port INTEGER NOT NULL,
//    local_time TEXT NOT NULL,
//    local_time_adjusted TEXT NOT NULL,
//    local_version TEXT,  -- Für SSH-Protokoll relevant
//    password TEXT,  -- Kann leer sein, wenn kein Passwort verwendet wurde
//    remote_version TEXT,  -- Für SSH-Protokoll relevant
//    username TEXT,
//    honeycred BOOLEAN,  -- Nur für Telnet (Port 23) relevant, standardmäßig FALSE
//    logtype INTEGER NOT NULL,
//    node_id TEXT NOT NULL,
//    src_host TEXT NOT NULL,
//    src_port INTEGER NOT NULL,
//    utc_time TEXT PRIMARY KEY
//);");
$db = new SQLite3(__DIR__ . '/grafana-storage/import/logs.db', SQLITE3_OPEN_READWRITE, null);


$handle = fopen(__DIR__ . '/import/opencanary.log', "r");
$stmt = $db->prepare("REPLACE INTO network_logs (dst_host, dst_port, local_time, local_time_adjusted, local_version, password, remote_version, username, honeycred, logtype, node_id, src_host, src_port, utc_time) VALUES (:dst_host, :dst_port, :local_time, :local_time_adjusted, :local_version, :password, :remote_version, :username, :honeycred, :logtype, :node_id, :src_host, :src_port, :utc_time)");

$lastLine = file_get_contents(__DIR__ . '/lastLine.txt');

// Überprüfe, ob das File erfolgreich geöffnet wurde
if ($handle) {
    $currentLine = 0;
    // Iteriere durch das File, zeile für zeile
    while (($line = fgets($handle)) !== false) {
        $currentLine++;
        if ($lastLine < $currentLine) {
            $data = json_decode($line, true);
            $stmt->bindValue(':dst_host', $data['dst_host'], SQLITE3_TEXT);
            $stmt->bindValue(':dst_port', $data['dst_port'], SQLITE3_INTEGER);
            $stmt->bindValue(':local_time', $data['local_time'], SQLITE3_TEXT);
            $stmt->bindValue(':local_time_adjusted', $data['local_time_adjusted'], SQLITE3_TEXT);
            $stmt->bindValue(':local_version', $data['logdata']['LOCALVERSION'] ?? null, SQLITE3_TEXT);
            $stmt->bindValue(':password', $data['logdata']['PASSWORD'] ?? null, SQLITE3_TEXT);
            $stmt->bindValue(':remote_version', $data['logdata']['REMOTEVERSION'] ?? null, SQLITE3_TEXT);
            $stmt->bindValue(':username', $data['logdata']['USERNAME'] ?? null, SQLITE3_TEXT);
            $stmt->bindValue(':honeycred', isset($data['honeycred']) ?? 0, SQLITE3_INTEGER);
            $stmt->bindValue(':logtype', $data['logtype'], SQLITE3_INTEGER);
            $stmt->bindValue(':node_id', $data['node_id'], SQLITE3_TEXT);
            $stmt->bindValue(':src_host', $data['src_host'], SQLITE3_TEXT);
            $stmt->bindValue(':src_port', $data['src_port'], SQLITE3_INTEGER);
            $stmt->bindValue(':utc_time', $data['utc_time'], SQLITE3_TEXT);
            $stmt->execute();
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