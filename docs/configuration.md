## Configuration (optional):

You can use bundle without an extra configuration. But if not all connections are encrypted when config list of them:
```yml
aeliot_doctrine_encrypted:
    encrypted_connections: [my_encrypted_connection]
```

Nether the less, you have to config checker of encryption availability and application secret provider via aliases.
For example:
```yaml
    Aeliot\Bundle\DoctrineEncrypted\Service\EncryptionAvailabilityCheckerInterface:
        alias: 'App\Doctrine\Encryption\EncryptionAvailabilityChecker'

    Aeliot\Bundle\DoctrineEncrypted\Service\SecretProviderInterface:
        alias: 'App\Doctrine\Encryption\SecretProvider'
```

See documentation of Symfony for [Aliasing](https://symfony.com/doc/current/service_container/alias_private.html#aliasing).

And decorate or implement necessary platform function provider.
See example of [MysqlFunctionProvider](../example/Doctrine/Encryption/MysqlFunctionProvider.php) for the project
with encryption key which divided on two parts:
- one in the app and is set the database connection session
- another one is in another database.

See documentation of Symfony [how to decorate service](https://symfony.com/doc/current/service_container/service_decoration.html).
