# Projet Symfony

Créer un nouveau projet WEB :

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
symfony console make:form RegistrationType
```

Génération d'un controller:

```bash
symfony console make:controller
```

# BDD

Allez dans le fichier .env et ajouter la connexion à la BDD

```yaml
DATABASE_URL="mysql://root:@127.0.0.1:3306/macave"
```

Création de la BDD:

```bash
symfony console doctrine:database:create
```

Création des tables de la BDD:

```bash
symfony console make:entity
symfony console make:migration
```

Mettre à jour la BDD:

```bash
symfony console doctrine:migrations:migrate
```

## Mettre des fausses données dans la BDD:

```bash
composer require --dev doctrine/doctrine-fixtures-bundle
```

Création de la classe pour ajouter des données dans notre table:

```bash
symfony console make:fixtures
```

Ajouter les données fixtures dans la BDD de test:

```bash
symfony console doctrine:fixtures:load --env=test
```

# Symfony Unit Test

Installation des dépendances:
```bash
composer require --dev symfony/test-pack
```

### Création de la BDD pour les tests et ajout fausses données:
```bash
symfony console --env=test doctrine:database:create
symfony console --env=test doctrine:schema:create
symfony console --env=test doctrine:fixtures:load
```

### Réinitialisation automatique de la base de données avant chaque test
```bash
composer require --dev dama/doctrine-test-bundle
```

Maintenant, activez-le en tant qu'extension PHPUnit :
```xml
<!-- phpunit.xml.dist -->
<phpunit>
    <!-- ... -->

    <extensions>
        <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
    </extensions>
</phpunit>
```

Pour créer des sénarios de test:
```bash
symfony console make:test
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
composer require symfony/password-hasher

composer require symfonycasts/verify-email-bundle
```

Contraite Formulaire:

- https://symfony.com/doc/current/reference/constraints.html

Creer la classe utilisateur avec mot de passe sécurisé/hasher:

- https://symfony.com/doc/current/security.html
- https://symfony.com/doc/current/security/passwords.html

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

Formulaire réinitialisation Mot de passe

```bash
composer require symfonycasts/reset-password-bundle
symfony console make:reset-password
```

# Activer bootstrap 5 avec NPM

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

Importons-le JavaScript suivant les consignes de la documentation de Bootstrap 5 en modifiant le
fichier `/assets/app.js.`

```js
// You can specify which plugins you need
import {Tooltip, Toast, Popover} from 'bootstrap';
```

Dépendance nécessaire:

```
npm install @popperjs/core --save
```

Créons un fichier `custom.scss` dans `/assets/styles`, puis importons les feuilles de style
dans `/assets/styles/app.scss`.

```js
import "custom";
import "~bootstrap/scss/bootstrap";
```

Documentation symfony: https://symfony.com/doc/current/form/form_themes.html

Afin que par symfony par défaut utilise le thème bootstrap, il faut ajouter ceci:
```yaml
# config/packages/twig.yaml
twig:
    form_themes: ['bootstrap_5_layout.html.twig']
```

Lancer en arrière plan le serveur pour voir les changements en live du css (En même temps que celui de symfony):

```bash
npm run dev-server
```

# Activer bootstrap 5 avec yarn

```
composer require symfony/webpack-encore-bundle
yarn install

yarn add bootstrap
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
yarn add sass-loader sass
```

Nous allons aussi installer PostCSS

```bash
yarn add postcss-loader autoprefixer
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
yarn run build
```

Importons-le JavaScript suivant les consignes de la documentation de Bootstrap 5 en modifiant le
fichier `/assets/app.js.`

```js
// You can specify which plugins you need
import {Tooltip, Toast, Popover} from 'bootstrap';
```

Dépendance nécessaire:

```
yarn add @popperjs/core
```

Créons un fichier `custom.scss` dans `/assets/styles`, puis importons les feuilles de style
dans `/assets/styles/app.scss`.

```js
import "custom";
import "~bootstrap/scss/bootstrap";
```

Documentation symfony: https://symfony.com/doc/current/form/form_themes.html

Afin que par symfony par défaut utilise le thème bootstrap, il faut ajouter ceci:
```yaml
# config/packages/twig.yaml
twig:
    form_themes: ['bootstrap_5_layout.html.twig']
```

Lancer en arrière plan le serveur pour voir les changements en live du css (En même temps que celui de symfony):

```bash
yarn run dev-server
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

This will copy all files from assets/images into public/build/images. If you have versioning enabled, the copied files
will include a hash based on their content.

Ajouter une images dans le projet, il faut les copier dans ce dossier: `assets/images`

Ajouter une image dans un fichier twig:
```twig
<link rel="icon" type="image/svg" href={{ asset('build/images/favicon.svg') }} />
```

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

# Traduction

Générer les fichiers de traduction selon la langue

```bash
symfony console translation:extract --force fr
```

Ensuite allez dans le dossier `translations` à la racine du projet, on y retrouve tout les fichiers de traduction. Les
fichiers qui nous intéresse sont ceux qui commence par `messages+intl-icu*.xlf`

Si ces fichiers n'existe pas alors vous n'avez pas encore mis en place de traduction dans votre code, voir ci-dessous

## Traduction Via fichier Twig

les balises `{% trans %} {% endtrans %}` permettent de dire à symfony ou il faut traduire le text.
Exemple:

```html
<div class="col-md-6">
    <label for="lastname" class="form-label">{% trans %} Prénom {% endtrans %}</label>
    {{ form_widget(registrationForm.firstname) }}
</div>
```
Ensuite réexecuter la commande pour générer les fichiers de traduction et traduisez vos messages !
Pour traduire vous avez juste à remplacer le champs `<target></target>` avec votre traduction dans le fichier `messages+intl-icu*.xlf`

## Traduction via code php

Certaine partie ne peuvent pas être géré directement via TWIG alors on doit passer par PHP. Pour mettre en place la traduction il faut faire que ceci:
```php
use Symfony\Component\Translation\TranslatableMessage;
$firstname_user_field  = new TranslatableMessage('Prénom');

return [
    TextField::new('firstname', $firstname_user_field->getMessage())
]
```
Ensuite réexecuter la commande pour générer les fichiers de traduction et traduisez vos messages !
Pour traduire vous avez juste à remplacer le champs `<target></target>` avec votre traduction dans le fichier `messages+intl-icu*.xlf`