
install: copy-config
	docker compose build

up:
	docker compose up -d --wait

down:
	docker compose down

copy-config:
	cp config/.opencanary.conf opencanary/data/.opencanary.conf

unzip-geo:
	if test -f geo/dbip-city-lite-2024-05.mmdb.bz2; then\
      bzip2 -d geo/dbip-city-lite-2024-05.mmdb.bz2\
    fi