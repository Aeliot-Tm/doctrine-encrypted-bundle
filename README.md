# Doctrine Encrypted Bundle

The bundle permits to encrypt separate columns of database via Doctrine column types.

## Installation

Call command line script to install:
```shell
composer require aeliot/doctrine-encrypted-bundle
```

See whole documentation [here](docs/index.md).

## Database options

Encrypted data has different size to origin. So, the bundle expects some options of database tables
to get for more stable result:
- charset: utf8mb4
- collation: utf8mb4_unicode_ci
