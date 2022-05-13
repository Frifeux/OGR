# Installation WSL et Docker Desktop

Doc officielle installation docker avec WSL: https://docs.docker.com/desktop/windows/wsl/

> Faite un point de réstauration Windows avant !!

Suivre ce [tuto](https://medium.com/@fred.gauthier.dev/web-development-environment-with-wsl-2-and-docker-for-symfony-5860704e127a) et s'arréter au moment de l'installation de docker dans la machine debian !
> Nous installerons **docker** sur linux plus tard !

## Problème d'installation WSL

> Seulement si WSL ne veut vraiment pas fonctionner !

1. Avoir activer la virtualisation CPU dans le BIOS
2. Désactiver le **Secure Boot** et **Fast boot** dans le BIOS
3. Bien avoir mis à jour son poste en dernière version de windows
4. Dans les options d'alimentation Windows décocher `activer le démarrage rapide`

Ensuite lancer dans cette ordre les commandes suivantes:
```
dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
BCDedit /set hypervisorlaunchtype Off
```

Redémarrer votre poste et normalement WSL devrait être installé !

# Installation dépendances Debian

Ici on va retrouver toutes les choses à installer qui seront nécessaires pour notre projet Symfony

## Installation PHP 8.1

```bash
apt install apt-transport-https lsb-release ca-certificates -y
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
```

```bash
apt update && apt upgrade
apt install php-xml php-intl php-gd php-curl php-fpm php-mysql php-ldap php-dev php-raphf php-http
```

## Installation Symfony CLI

```bash
wget https://get.symfony.com/cli/installer -O - | bash
mv /root/.symfony/bin/symfony /usr/local/bin/symfony
```

## Installation Composer:

Doc installation [composer](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-debian-10)
```bash
apt install curl php-cli php-mbstring git unzip
cd ~ && curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

## Installation Docker

Doc installation [docker debian](https://docs.docker.com/engine/install/debian/)
```bash
apt-get install \
    ca-certificates \
    curl \
    gnupg \
    lsb-release
```

```bash
curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
```

```bash
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/debian \
  $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
```

```bash
apt-get update
apt-get install docker-ce docker-ce-cli containerd.io
```


## Docker-compose

```bash
curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
```

Voir la version actuelle de docker-compose:
```bash
docker-compose --version
```

## Nodejs & NPM

```bash
curl -sL https://deb.nodesource.com/setup_16.x | bash -
apt-get install -y nodejs build-essential gcc g++ make
```

## Nodejs & YARN

> Il faut au préalable installer **NPM** !

```bash
curl -sL https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /usr/share/keyrings/yarnkey.gpg >/dev/null
echo "deb [signed-by=/usr/share/keyrings/yarnkey.gpg] https://dl.yarnpkg.com/debian stable main" | tee /etc/apt/sources.list.d/yarn.list
apt-get update && apt-get install yarn
```

# Mise en place projet depuis zéro

> Il faut avoir installé toutes les dépendances debian situé ci-dessus !

On peut retrouver un tuto [ici](https://jean-pierre.lambelet.net/astuces/php/commencer-un-nouveau-projet-symfony5-avec-docker-compose-nginx-php-7-4-et-mariadb-692/) qui explique très bien la mise en place de symfony avec docker

> Avant de commencer la suite, il faut vérifier que docker fonctionne correctement, il faut éxécuter cette commande: **sudo docker run hello-world**

Création de l'arborescence du projet:
```bash
mkdir OGR && cd OGR
mkdir docker && mkdir docker/{nginx,php-fpm}
```

Mise en place de notre docker **nginx**:

```bash
nano docker/nginx/Dockerfile

FROM nginx:alpine
CMD ["nginx"]
EXPOSE 80 443
```
Mise en place de notre docker **php-fpm**:

```dockerfile
FROM php:8.1-fpm

#https://github.com/mlocati/docker-php-extension-installer

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions http intl opcache pdo pdo_mysql ldap apcu zip xdebug
```

Configuration Nginx

```bash
cd docker/nginx && mkdir sites
nano sites/default.conf
```

```nginx
server {
    listen 80 default_server;
    #listen [::]:80 default_server ipv6only=on;

    server_name localhost;
    root /var/www/symfony/public;

    index index.php index.html index.htm;

    location / {
         try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass php-fpm:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;

        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;

        internal;
    }

    location ~ \\.php$ {
        return 404;
    }
}
```

A la racine du dossier **nginx** nous allons créer un fichier configuration `nginx.conf`:

```nginx
user  nginx;
worker_processes  4;
daemon off;

error_log  /var/log/nginx/error.log warn;
pid        /var/run/nginx.pid;

events {
    worker_connections  1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;
    access_log  /var/log/nginx/access.log;
    # Switch logging to console out to view via Docker
    #access_log /dev/stdout;
    #error_log /dev/stderr;
    sendfile        on;
    keepalive_timeout  65;

    include /etc/nginx/sites-available/*.conf;
}
```

Importation de la structure du projet Symfony:

```bash
cd ../..
symfony new OGR --full --no-git
```

## Fichier docker-compose.yml

A la racine du dossier de votre projet, il faut créer un fichier `docker-compose.yml` avec cette configuration:

> Il faudra changer les mots de passe des dockers phpmyadmin et database !

```yaml
version: '3.8'
services:

  php-fpm:
    container_name: php-fpm_symfony
    build:
      context: ./docker/php-fpm
    ports:
      - '9000:9000'
    volumes:
      - ./:/var/www/symfony
    restart: always

  nginx:
    container_name: nginx_symfony
    build:
      context: ./docker/nginx
    volumes:
      - ./:/var/www/symfony
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./docker/nginx/sites/:/etc/nginx/sites-available
    depends_on:
      - php-fpm
    ports:
      - "8080:80"
      - "8443:443"
    restart: always

  database:
    container_name: database_symfony
    image: mariadb:latest
    environment:
      - MYSQL_DATABASE=ogrdb
      - MYSQL_USER=ogr
      - MYSQL_PASSWORD=993Djc97ncXhdydfsjPhtFr4
      - MYSQL_ROOT_PASSWORD=LQEzc6pJp8tBHh4zgEEQme7L
    ports:
      - "3306:3306"
    restart: always
    
  phpmyadmin:
    container_name: phpmyadmin_symfony
    image: phpmyadmin:latest
    environment:
      PMA_HOST: database_symfony
      PMA_USER: ogr
      PMA_PASSWORD: 993Djc97ncXhdydfsjPhtFr4
    ports:
      - "8081:80"
    restart: always

  maildev:
    image: maildev/maildev
    command: bin/maildev --web 80 --smtp 25 --hide-extensions STARTTLS
    container_name: maildev_symfony
    ports:
      - "1080:80"
      - "1025:25"
    restart: always
```

Nous avons plus qu'a démarrer nos dockers, il faut ce situé au même endroit que le fichier `docker-compose.yml`:
```bash
docker-compose up -d
```

# Importer un projet via GIT

> Il faut avoir installé toutes les dépendances debian situé ci-dessus !

Importation du projet GIT:
```bash
git clone https://github.com/Frifeux/OGR.git
```

Quand vous importer un projet symfony toute les dépendances lié a celui-ci ne sont pas importées. Il faut donc les installées, ce rendre dans le dossier `OGR` et lancer cette commande:
```bash
composer install
yarn install
yarn run build
```

Importation de la BDD
```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

Ajout des droits au dossier et fichiers:
```bash
chown www-data:www-data ../OGR -R
```

Ensuite démarrer nos dockers en se mettant au même endroit que le fichier `docker-compose.yml`:
```bash
docker-compose up -d
```

> Et voila le tour est joué, votre projet est lancé !

# Passer en version PROD

Editer le fichier `.env` et modifier la variable `APP_ENV` à `prod`
Ensuite il faudra aussi modifier `APP_SECRET` et mettre un chaine de caractères aléatoire

> Cette commande est très importante sinon vous allez avoir des erreurs en passant de la version `DEV` à `PROD`

Pour finir, il faut exécuter cette commande symfony, elle a pour but de supprimer le cache:

```bash
symfony console cache:clear
```

> A vous de désactiver les dockers qui ne seront plus utiles en version de **production**, commenter les lignes dans le fichiers `docker-compose.yml` et faite à nouveau `docker-compose up -d`

# TIPS

Accéder au fichier WSL depuis Windows:
```bash
\\wsl$\Debian
```

# Documentation Annexe

- Aide création des dockers:
https://yoandev.co/un-environnement-de-developpement-symfony-5-avec-docker-et-docker-compose

- maildev docker:
https://hub.docker.com/r/maildev/maildev

- phpmyadmin docker:
https://hub.docker.com/r/phpmyadmin/phpmyadmin/
