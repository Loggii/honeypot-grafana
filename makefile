HOST=192.168.2.193
USER=dev
CURRENT_DIR = $(shell pwd)


sync:
	ssh -i ~/.ssh/id_ed25519_deploy  -p 22 $(USER)@$(HOST) 'docker cp opencanary_latest:/var/tmp/opencanary.log /home/dev/honeypot/opencanary/opencanary.log'
	scp -i ~/.ssh/id_ed25519_deploy $(USER)@$(HOST):/home/dev/honeypot/opencanary/opencanary.log $(CURRENT_DIR)/import/
	php sync.php