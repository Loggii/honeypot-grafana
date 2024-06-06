#!/usr/bin/env bash

echo "" >| /app/logs/opencanary.log
rm /app/lastLine.txt

while [ true ]; do
    sleep 30;
    if [ -f /app/logs/opencanary.log ]; then
        php /app/sync.php;
    else
        echo 'Warte auf Logs'
    fi

done