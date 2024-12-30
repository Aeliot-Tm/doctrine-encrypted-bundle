# Doctrine Encrypted Bundle

[![WFS](https://github.com/Aeliot-Tm/doctrine-encrypted-bundle/actions/workflows/automated_testing.yml/badge.svg?branch=main)](https://github.com/Aeliot-Tm/doctrine-encrypted-bundle/actions)
[![Code Climate maintainability](https://img.shields.io/codeclimate/maintainability/Aeliot-Tm/doctrine-encrypted-bundle?label=Maintainability&labelColor=black)](https://codeclimate.com/github/Aeliot-Tm/doctrine-encrypted-bundle)
[![GitHub License](https://img.shields.io/github/license/Aeliot-Tm/doctrine-encrypted-bundle?label=License&labelColor=black)](LICENSE)

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
