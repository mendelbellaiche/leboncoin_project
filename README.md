Symfony Demo Application
========================

Requirements
------------

* PHP 8.2.0
* Docker

Installation
------------

Après avoir télécharger le projet, il faut installer launcer le scipt docker pour créer un container mysql et, après l'installation du container MySQL, créer les tables:

```
$ docker compose up -d

$ symfony console make:migration
$ symfony console doctrine:migrations:migrate
```

Usage
------------

Après l'installation de la base de donnée, il faut lancer le server:

```
$ symfony server:start
```

Voici une liste de requête HTTP:

```
# Requête GET pour récupérer l'ensemble des annonces

$ curl --location --request GET 'http://localhost:8000/api'

# Requête GET pour récupérer une annonce

$ curl --location --request GET 'http://localhost:8000/api/4'

# Requête GET pour créer une annonce

curl --location --request POST 'http://localhost:8000/api' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'title=title' \
--data-urlencode 'content=content' \
--data-urlencode 'category=Immobilier'

curl --location --request POST 'http://localhost:8000/api' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'title=title' \
--data-urlencode 'content=content' \
--data-urlencode 'category=Automobile' \
--data-urlencode 'model=ds 3 crossback'

# Requête GET pour modifier une annonce

curl --location --request PUT 'http://localhost:8000/api/1' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'title=title2' \
--data-urlencode 'content=content2' \
--data-urlencode 'category=Immobilier'

curl --location --request PUT 'http://localhost:8000/api/1' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'title=title2' \
--data-urlencode 'content=content2' \
--data-urlencode 'category=Automobile' \
--data-urlencode 'model=ds 3 crossback'

# Requête GET pour supprimer une annonce

curl --location --request DELETE 'http://localhost:8000/api/1'

```

Tests
------------

```
$ php bin/phpunit tests/Entity/AnnoncesTest.php --verbose
```