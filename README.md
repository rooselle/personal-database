# Personal Database

Application personnelle pour archiver mes lectures et visionnages, construite avec Laravel, Livewire et Flux UI.

## Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) ou [Docker Engine](https://docs.docker.com/engine/)

## Installation (premier lancement)

### 1. Cloner le dépôt

```bash
git clone <url>
cd personal-database
```

### 2. Installer les dépendances PHP via Docker

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
```

### 4. Construire et démarrer les conteneurs

```bash
./vendor/bin/sail up -d
```

La première exécution peut prendre quelques minutes (construction de l'image Docker).

### 5. Initialiser l'application

```bash
./vendor/bin/sail composer run setup
```

L'application est disponible sur **http://localhost**.

---

## Utilisation quotidienne

**Démarrer les conteneurs :**
```bash
./vendor/bin/sail up -d
```

**Lancer le serveur de développement** (Vite + queue worker + logs) :
```bash
./vendor/bin/sail composer run dev
```

**Arrêter les conteneurs :**
```bash
./vendor/bin/sail down
```

**Commandes Artisan :**
```bash
./vendor/bin/sail artisan <commande>
```

**Peupler la base avec des données de test :**
```bash
./vendor/bin/sail artisan db:seed
```

**Vider la base et recommencer le seed :**
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

**Tests :**
```bash
./vendor/bin/sail artisan test --compact
```

**Alias pratique** (à ajouter dans `~/.zshrc` ou `~/.bashrc`) :
```bash
alias sail='./vendor/bin/sail'
```

Ensuite toutes les commandes peuvent s'écrire `sail up -d`, `sail artisan migrate`, etc.
