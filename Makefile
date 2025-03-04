.PHONY: build up down restart logs ps clean

# Сборка и запуск контейнеров
build:
	make down
	docker-compose up -d --build

# Запуск уже собранных контейнеров
up:
	docker-compose up -d

# Остановка контейнеров
down:
	docker-compose down

# Перезапуск контейнеров
restart:
	docker-compose down && docker-compose up -d

# Логи контейнеров
logs:
	docker-compose logs -f

# Статус контейнеров
ps:
	docker-compose ps

# Очистка всех данных (осторожно!)
clean:
	docker-compose down -v --rmi all --remove-orphans
bash:
	docker exec -it pdf-generator-php bash

