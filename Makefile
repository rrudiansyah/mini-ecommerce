# ─────────────────────────────────────────────────────
# Makefile — Shortcut perintah Docker untuk Windows
# Jalankan via: make <perintah>
# Atau copy perintah docker compose-nya langsung
# ─────────────────────────────────────────────────────

.PHONY: up down build restart logs shell db-shell fresh

## Jalankan semua container (development)
up:
	docker compose up -d

## Hentikan semua container
down:
	docker compose down

## Build ulang image (setelah ubah Dockerfile)
build:
	docker compose build --no-cache

## Build + jalankan
rebuild:
	docker compose down && docker compose build --no-cache && docker compose up -d

## Restart container app saja
restart:
	docker compose restart app

## Lihat log real-time
logs:
	docker compose logs -f app

## Masuk ke shell container app
shell:
	docker compose exec app sh

## Masuk ke MySQL CLI
db-shell:
	docker compose exec db mysql -u ecommerce_user -pecommerce_pass ecommerce_builder

## Import ulang schema database
db-fresh:
	docker compose exec db mysql -u root -psecret ecommerce_builder < database.sql

## Lihat status semua container
status:
	docker compose ps

## Stop dan hapus semua data (HATI-HATI: data DB hilang!)
nuke:
	docker compose down -v --remove-orphans
