
install: copy-config
	docker compose build

up:
	docker compose up -d --wait

down:
	docker compose down

copy-config:
	cp config/.opencanary.conf opencanary/data/.opencanary.conf