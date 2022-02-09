# Projet Symfony

Créer un nouveau projet WEB:
```bash
symfony new my_project_name --full # Recup la dernière version dispo
symfony new macave_daisyui_5.3 --full --version=5.3 #Pour spécifier une version
```

Démarrer le serveur:
```bash
symfony server:start
```

Génération d'un formulaire:
```bash
php bin/console make:form RegistrationType
```

Génération d'un controller:
```bash
php bin/console make:controller
```

# BDD

Allez dans le fichier .env et ajouter la connexion à la BDD
```json
DATABASE_URL="mysql://root:@127.0.0.1:3306/macave"
```

Création de la BDD:
```bash
php bin/console doctrine:database:create
```

Création des tables de la BDD:
```bash
symfony console  make:entity
symfony console  make:migration
```

Mettre à jour la BDD:
```bash
symfony console doctrine:migrations:migrate
```

Mettre des fausses données dans la BDD:
```bash
composer require orm-fixtures --dev
```

Création de la classe pour ajouter des données dans notre table:
```bash
symfony console make:fixtures
```

Ajouter les données fixtures dans la BDD:
```bash
symfony console doctrine:fixtures:load
```

# BDD Code (Manipulation de données)

## OLD
```php
ObjectManager $manager

$manager->persist();
$manager->flush();
```

## NEW
```php
$entityManager = $this->getDoctrine()->getManager();
$entityManager->flush();
```

## Selection de données
```php
$repo = $this->getDoctrine()->getRepository(User::class);
$user = $repo->findOneBy(['firstname' => 'test']);
```

# Formulaire Connexion / Deconnexion / Inscription

Dépendance composer necessaires:
```bash
composer require security annotations doctrine
composer require --dev web-profiler
composer require symfony/security-guard

composer require symfonycasts/verify-email-bundle
```

Contraite Formulaire:
- https://symfony.com/doc/current/reference/constraints.html
	
Creer la classe utilisateur avec mot de passe sécurisé:
- https://symfony.com/doc/current/security.html
```bash
symfony console make:user
```

Creer le formulaire de connexion et deconnexion
```bash
symfony console make:auth
```

Creer le formulaire d'inscription
```bash
symfony console make:registration-form
```


# Activer bootstrap 5

```
composer require symfony/webpack-encore-bundle
npm install

npm install bootstrap
```

Dans le fichier base.html.twig situé: `src/templates` décommenter pour obtenir ceci:

```twig
{% block stylesheets %}
    {{ encore_entry_link_tags('app') }}
{% endblock %}

{% block javascripts %}
    {{ encore_entry_script_tags('app') }}
{% endblock %}
```

Renommons le fichier `/assets/styles/app.css` en `app.scss`, et modifions le fichier `/assets/app.js`.

```js
import './styles/app.scss';
```

Dé-commenter `.enableSassLoader()` dans le fichier webpack.config.js et installons sass-loader.

```bash
npm install sass-loader sass
```

Nous allons aussi installer PostCSS

```bash
npm install postcss-loader autoprefixer
```

Créons un fichier `postcss.config.js` à la racine du projet.
```js
module.exports = {
    plugins: {
        autoprefixer: {}
    }
}
```

```
npm run build
```

Importons-le JavaScript suivant les consignes de la documentation de Bootstrap 5 en modifiant le fichier `/assets/app.js.`

```js
// You can specify which plugins you need
import { Tooltip, Toast, Popover } from 'bootstrap';
```

Créons un fichier `custom.scss` dans `/assets/styles`, puis importons les feuilles de style dans `/assets/styles/app.scss`.

```js
@import "custom";
@import "~bootstrap/scss/bootstrap";
```

Lancer en arrière plan le serveur pour voir les changements en live du css (En même temps que celui de symfony):
```bash
npm run dev-server
```

# Activer Daisyui et Tailwind

- https://yoandev.co/bootstrap-5-avec-symfony-5-et-webpack-encore
- https://www.youtube.com/watch?v=SkJti2mrnNM

Dépendances nécessaires:
```bash
composer require symfony/webpack-encore-bundle
npm install
```

Lancer en arrière plan le serveur pour voir les changements en live du css (En même temps que celui de symfony):
```bash
npm run dev-server
```

Juste compiler les fichiers CSS pour la version production:
```bash
npm run build
```

Installation des dépendances tailwind:
```bash
npm install -D tailwindcss@latest postcss@latest autoprefixer@latest
npm install -D postcss-loader@latest
npm install -D sass-loader@latest sass typescript ts-loader@latest
```

Génération des config tailwind:
```bash
npx tailwindcss init -p
```

Décommenter ça dans le fichier webpack.config.js
```js
.enablePostCssLoader()

// enables Sass/SCSS support
.enableSassLoader()

// uncomment if you use TypeScript
.enableTypeScriptLoader()
```

Dans le fichier base.html.twig situé: `src/templates` décommenter pour obtenir ceci:

```twig
{% block stylesheets %}
    {{ encore_entry_link_tags('app') }}
{% endblock %}

{% block javascripts %}
    {{ encore_entry_script_tags('app') }}
{% endblock %}
```

Dans le fichier app.css situé: `assets/styles` mettre:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

Ajouter Daisyui:
```bash
npm i daisyui
```

Dans le fichier tailwind.config.js ajouter:
```js
plugins: [
    require('daisyui'),
],
```

# Ajout d'images:

Installation des dépendances npm:
```bash
npm install file-loader
```

Dans le fichier webpack.config.js ajouté ça:

```js
.copyFiles({
    from: './assets/images',

    // optional target path, relative to the output dir
    to: 'images/[path][name].[ext]',

    // if versioning is enabled, add the file hash too
    //to: 'images/[path][name].[hash:8].[ext]',

    // only copy files matching this pattern
    //pattern: /\.(png|jpg|jpeg)$/
})
```
This will copy all files from assets/images into public/build/images. If you have versioning enabled, the copied files will include a hash based on their content.

# EasyAdminPanel

Activer module php intl, sur xampp: `Panel -> Config -> php.ini`

Plus qu'a décommenter cette ligne:
```
extension=intl
```
Pour finir, Redemarrer le server symfony



## Installation du bundle:
```bash
composer require easycorp/easyadmin-bundle
```

## Creation du panel:
```bash
symfony console make:admin:dashboard
```

Ajout d'item dans le menu du dashboard
```php
public function configureMenuItems(): iterable
{
    yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

    yield MenuItem::section('Gestion utilisateur');
    yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
    // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
}
```

## Ajout de la gestion des utilisateurs:
```bash
symfony console make:admin:crud
```
