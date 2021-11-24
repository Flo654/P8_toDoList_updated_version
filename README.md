
# P8 ToDoList

Améliorez un projet existant

Vous venez d’intégrer une startup dont le cœur de métier est une application permettant de gérer ses tâches quotidiennes. L’entreprise vient tout juste d’être montée, et l’application a dû être développée à toute vitesse pour permettre de montrer à de potentiels investisseurs que le concept est viable (on parle de Minimum Viable Product ou MVP).

Le choix du développeur précédent a été d’utiliser le framework PHP Symfony, un framework que vous commencez à bien connaître ! 

Bonne nouvelle ! ToDo & Co a enfin réussi à lever des fonds pour permettre le développement de l’entreprise et surtout de l’application.

Votre rôle ici est donc d’améliorer la qualité de l’application. La qualité est un concept qui englobe bon nombre de sujets : on parle souvent de qualité de code, mais il y a également la qualité perçue par l’utilisateur de l’application ou encore la qualité perçue par les collaborateurs de l’entreprise, et enfin la qualité que vous percevez lorsqu’il vous faut travailler sur le projet.

Ainsi, pour ce dernier projet de spécialisation, vous êtes dans la peau d’un développeur expérimenté en charge des tâches suivantes :

l’implémentation de nouvelles fonctionnalités ;
la correction de quelques anomalies ;
et l’implémentation de tests automatisés.
Il vous est également demandé d’analyser le projet grâce à des outils vous permettant d’avoir une vision d’ensemble de la qualité du code et des différents axes de performance de l’application.

Il ne vous est pas demandé de corriger les points remontés par l’audit de qualité de code et de performance. Cela dit, si le temps vous le permet, ToDo & Co sera ravi que vous réduisiez la dette technique de cette application.
## Authors

- [@florentascensio](https://www.github.com/Flo654)


## Badges


[![MIT License](https://img.shields.io/apm/l/atomic-design-ui.svg?)](https://github.com/tterb/atomic-design-ui/blob/master/LICENSEs)

[![Maintainability](https://api.codeclimate.com/v1/badges/71d99d3ecd4d6b3a0e38/maintainability)](https://codeclimate.com/github/Flo654/P8_toDoList_updated_version/maintainability)


## Run Locally

Clone the project

```bash
  git clone https://github.com/Flo654/P8_toDoList_updated_version.git
```

Go to the project directory

```bash
  cd P8_toDoList_updated_version
```

Install dependencies

```bash
  composer install
```



## Environment Variables

To run this project, you will need to add the following environment variables to your .env.local file

`DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7" `

don't forget to delete .local extension to.env.local file before to run the project
## Run the project

Create database and data:

`composer prepare`

Start the server

`symfony serve -d` 
## Running Tests

This app has been configured with sqlite database for the tests.
To run tests, run the following command in your terminal

:warning: You can already find a coverage test in HTML format in folder web/test-coverage

to create database and load fixtures:

`composer prepare-test`

to execute tests:

if you just want to run the tests in terminal :

```bash
  vendor/bin/phpunit
```

if you want in HTML format, run:

```bash
  vendor/bin/phpunit --coverage-html web/test-coverage
```

## Documentation

[SecurityBundle](https://symfony.com/doc/current/security.html)

[blackFire](https://www.blackfire.io/)


## Contributing

Contributions are always welcome!

See `CONTRIBUTING.md` for ways to get started.