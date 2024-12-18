## Configuration (optional):

You can use bundle without an extra configuration. But the most common one is like this:

```yml
aeliot_doctrine_encrypted:
    encryption_availability_checker: App\Doctrine\Encryption\EncryptionAvailabilityChecker
    secret_provider: App\Doctrine\Encryption\SecretProvider
```

And decorate or implement necessary platform function provider.
See example of [MysqlFunctionProvider](../example/Doctrine/Encryption/MysqlFunctionProvider.php) for the project
with encryption key which divided on two parts:
- one in the app and is set the database connection session
- another one is in another database.

See documentation of Symfony [how to decorate service](https://symfony.com/doc/current/service_container/service_decoration.html).
