## Configuration (optional):

You can use bundle without an extra configuration. But the most common one is like this:

```yml
aeliot_doctrine_encrypted:
    encryption_availability_checker: App\Doctrine\Encryption\EncryptionAvailabilityChecker
    functions_provider: App\Doctrine\Encryption\FunctionsProvider
    secret_provider: App\Doctrine\Encryption\SecretProvider
```
See example of [FunctionProvider](../example/Doctrine/Encryption/FunctionProvider.php) for the project
with encryption key which divided on two parts:
- one in the app and is set the database connection session
- another one is in another database.
