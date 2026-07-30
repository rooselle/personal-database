# Personal Database

Personal application to backup my reading and watching history, built with Laravel, Livewire and Flux UI.

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) or [Docker Engine](https://docs.docker.com/engine/)

## Installation (first launch)

### 1. Clone the repository

```bash
git clone <url>
cd personal-database
```

### 2. Install PHP dependencies via Docker

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Configure the environment

```bash
cp .env.example .env
```

### 4. Build and start the containers

```bash
./vendor/bin/sail up -d
```

The first run may take a few minutes (building the Docker image).

### 5. Initialize the application

```bash
./vendor/bin/sail composer run setup
```

The application is available at **http://localhost**.

---

## Daily usage

**Start the containers:**
```bash
./vendor/bin/sail up -d
```

**Run the development server** (Vite + queue worker + logs):
```bash
./vendor/bin/sail composer run dev
```

**Stop the containers:**
```bash
./vendor/bin/sail down
```

**Artisan commands:**
```bash
./vendor/bin/sail artisan <command>
```

**Seed the database with test data:**
```bash
./vendor/bin/sail artisan db:seed
```

**Wipe the database and reseed:**
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

**Tests:**
```bash
./vendor/bin/sail artisan test --compact
```

**Handy alias** (add to `~/.zshrc` or `~/.bashrc`):
```bash
alias sail='./vendor/bin/sail'
```

Then all commands can be written as `sail up -d`, `sail artisan migrate`, etc.
