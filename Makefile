start:
	docker compose up -d

stop:
	docker compose down

restart:
	docker compose restart

build:
	docker compose build --no-cache

exec:
	docker compose exec karo-road-web bash

down:
	docker compose down