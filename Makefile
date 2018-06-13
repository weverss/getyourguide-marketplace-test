install:
	docker run --rm -it -v $(shell pwd):/app composer install

up:
	docker run --rm -itw /app -v $(shell pwd):/app php:7.2-cli /bin/bash
