# Component Clock Symfony 7

Support to Medium topic Clock Component on Symfony 7

[Read the article](https://medium.com/@tabodino/composant-clock-avec-symfony-7-d9e96cfc649d) (French version)

![Screenshot](/public/images/screenshot.png)

## Requirements

- PHP 8.2.0 or higher
- SQLite module
- [Symfony CLI](https://symfony.com/download)
- [Symfony technical requirements](https://symfony.com/doc/current/setup.html#technical-requirements)

## Installation

#### 1. Install dependencies

```bash
composer install
```

#### 2. Run migrations

```bash
 php bin/console doctrine:migrations:migrate
```

#### 3. Load fixtures

```bash
 php bin/console doctrine:fixtures:load
```

#### 4. Enabling TLS

```bash
symfony server:ca:install
```

#### 5. Run local server

```bash
symfony serve -d
```

## Tests

Execute this command to run tests:

```bash
php bin/phpunit
```
