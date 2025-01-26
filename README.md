# Aplikacja Symfony z Dockerem 🐳

## Wymagania wstępne
- Zainstalowany [Docker](https://docs.docker.com/get-docker/) i [Docker Compose](https://docs.docker.com/compose/install/)

---

## Szybki start 🚀

### 1. Zbuduj kontenery Dockera
```
make build
```

### 2. Uruchom aplikację
```
make start
```
### 3. Wejdź do kontenera PHP
```
make exec
```

### 4. Wewnątrz kontenera wykonaj:

# Uruchom migracje bazodanowe
```
bin/console doctrine:migration:migrate --no-interaction
```
# Wygeneruj klucze JWT
```
bin/console lexik:jwt:generate-keypair --skip-if-exists
```
# Uruchom konsumenta wiadomości (w tle lub nowym terminalu)
```
bin/console messenger:consume async -vv
```
