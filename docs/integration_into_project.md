## Integration into project

The package is flexible. You can use single or split secret for data encryption.
There is described the simple integration with default settings.

1. Define environment variable `DB_ENCRYPTION_KEY`
2. Generate migration which install custom functions into database
3. Define column encrypted type of doctrine entity
   - via attributes
     ```php
     use Doctrine\ORM\Mapping as ORM;

     #[ORM\Entity()]
     class MyEntity
     {
         //...

         #[Orm\Column(type: 'encrypted_string')]
         private string $secret;
     }
     ```

   - or via annotation
     ```php
     use Doctrine\ORM\Mapping as ORM;

     /**
      * @ORM\Entity()
      */
     class MyEntity
     {
         //...

         /**
           * @Orm\Column(type="encrypted_string")
           */
         private string $secret;
     }
     ```

4. Generate migration which convert columns in database and encrypt data.
   ```php
    use Doctrine\DBAL\Schema\Schema;
    use Doctrine\Migrations\AbstractMigration;

    final class Version20240101000000 extends AbstractMigration
    {
        public function up(Schema $schema): void
        {
            $this->addSql('ALTER TABLE my_entity CHANGE secret secret VARBINARY(1024) DEFAULT NOT NULL');
            $this->addSql('UPDATE my_entity SET secret = AELIOT_ENCRYPT(secret) WHERE 1;');
        }

        public function down(Schema $schema): void
        {
            $this->addSql('UPDATE my_entity SET secret = AELIOT_DECRYPT(secret) WHERE 1;');
            $this->addSql('ALTER TABLE my_entity CHANGE secret secret VARCHAR(255) DEFAULT NOT NULL');
        }
    }
    ```

So, the data will be encrypted in the database and decrypted all over the project code.
You don't need to change data type of you field of entity and don't need to make another updates of your project.
