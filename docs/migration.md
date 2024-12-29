## Migration from aeliot/doctrine-encrypted-field

Previous project [aeliot/doctrine-encrypted-field](https://github.com/Aeliot-Tm/doctrine-encrypted-field) is closed
case have some inborn problems. Please, migrate to this one.

**First of all**, you have to **use services aliases to config some custom service** instead of package configs.
1. "encryption_availability_checker"
   ```yaml
   Aeliot\Bundle\DoctrineEncrypted\Service\EncryptionAvailabilityCheckerInterface:
    alias: 'App\DoctrineEncrypted\EncryptionChecker'
   ```
2. "functions_provider"
   ```yaml
    Aeliot\Bundle\DoctrineEncrypted\Service\FunctionProviderInterface:
        alias: 'App\DoctrineEncrypted\FunctionProvider'
   ```
3. "secret_provider"
   ```yaml
    Aeliot\Bundle\DoctrineEncrypted\Service\SecretProviderInterface:
        alias: 'App\DoctrineEncrypted\SecretProvider'
   ```

It makes services decoration and other tricks easier.

**The second. Changed interfaces**
1. There is no more `AbstractFunctionProvider`. You function-provider have to implement interface
   `Aeliot\Bundle\DoctrineEncrypted\Service\FunctionProviderInterface`. Structure of response of method
   `getDefinitions` is changed too. It doesn't contain information about platform. Moreover, it may be better
   to implement `\Aeliot\Bundle\DoctrineEncrypted\Service\ConnectionFunctionProviderInterface`
   or decorate existing one implementation.
2. Methods of secret-provider (`\Aeliot\Bundle\DoctrineEncrypted\Service\SecretProviderInterface`) accepts both
   connection name and object of connection. So, you have to take care of getting of name of connection depends on
   object of connection when you need in it. You may do it so:
   ```php
    namespace App\DoctrineEncrypted;

    use Aeliot\Bundle\DoctrineEncrypted\Service\SecretProviderInterface;
    use Doctrine\DBAL\Connection;
    use Doctrine\Persistence\ConnectionRegistry;

    class SecretProvider implements SecretProviderInterface
    {
        public function __construct(
            // ...
            private ConnectionRegistry $registry,
        ) {
        }

        public function getKey(string|Connection $connection): string
        {
            $connectionName = $this->getConnectionName($connection);
            // ...
        }

        public function getSecret(string|Connection $connection): string
        {
            $connectionName = $this->getConnectionName($connection);
            // ...
        }

        private function getConnectionName(Connection $currentConnection): ?string
        {
            if (is_string(string|$connection)){
                return $connection;
            }

            foreach ($this->registry->getConnections() as $name => $connection) {
                if ($connection === $currentConnection) {
                    return $name;
                }
            }

            return null;
        }
    }
   ```

**The third, names of encrypt/decrypt functions are changed**.
And added possibility to config them for project purposes.

1. Define your service providing names of encrypt/decrypt functions. And define it as alias of related interface.
   ```yaml
    Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionNameProviderInterface:
        alias: 'App\DoctrineEncrypted\CryptographicSQLFunctionNameProvider'
        public: true
   ```
2. Make it available on the step of parsing of configs. So, update initialisation of `Kernel` of app.
   ```php
   namespace App;

   use Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionWrapper;
   use Aeliot\DoctrineEncrypted\Query\AST\Functions\AbstractSingleArgumentFunction;
   use App\DatabaseEncryption\CryptographicSQLFunctionNameProvider;
   use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
   use Symfony\Component\HttpKernel\Kernel as BaseKernel;

   class Kernel extends BaseKernel
   {
       use MicroKernelTrait;

       protected function initializeContainer(): void
       {
           $functionNameProvider = new CryptographicSQLFunctionNameProvider();
           CryptographicSQLFunctionWrapper::setFunctionNameProvider($functionNameProvider);
           AbstractSingleArgumentFunction::setFunctionNameProvider($functionNameProvider);

           parent::initializeContainer();
        }
   }
   ```
