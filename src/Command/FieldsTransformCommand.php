<?php

declare(strict_types=1);

/*
 * This file is part of the Doctrine Encrypted Bundle.
 *
 * (c) Anatoliy Melnikov <5785276@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aeliot\Bundle\DoctrineEncrypted\Command;

use Aeliot\Bundle\DoctrineEncrypted\Service\EncryptedConnectionsRegistry;
use Aeliot\Bundle\DoctrineEncrypted\Service\TableEncryptor;
use Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionNameProviderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ConnectionRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class FieldsTransformCommand extends Command
{
    public function __construct(
        private EncryptedConnectionsRegistry $encryptedConnectionsRegistry,
        protected CryptographicSQLFunctionNameProviderInterface $functionNameProvider,
        private ConnectionRegistry $registry,
        private TableEncryptor $tableEncryptor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        /* TODO: process list of connections
         *       1. accept array of connections
         *       2. use list of encrypted connections (`...encrypted_connections`) as default value
         */
        $this->addArgument('connection', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Connection names');
        $this->addOption(
            'fields',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Table fields to be transformed. Example: --fields="table_1:field_1,field_2,field_3"'
        );
        $this->addOption('dump-sql', null, InputOption::VALUE_NONE, 'Dump sql instead of its execution');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $anOutput = $input->getOption('dump-sql') ? $output : new NullOutput();
        $tableFields = $input->getOption('fields');
        $function = $this->getFunction();

        foreach ($this->getConnectionName($input) as $connectionName) {
            /** @var Connection $connection */
            $connection = $this->registry->getConnection($connectionName);
            foreach ($tableFields as $option) {
                [$table, $fieldsList] = explode(':', $option, 2);
                $fields = explode(',', $fieldsList);
                $this->tableEncryptor->convert($connection, $table, $fields, $function, $anOutput);
            }
        }

        return self::SUCCESS;
    }

    abstract protected function getFunction(): string;

    /**
     * @return string[]
     */
    private function getConnectionName(InputInterface $input): array
    {
        $connections = $input->getArgument('connection');
        $encryptedConnections = $this->encryptedConnectionsRegistry->getNames();
        $invalidConnections = array_diff($connections, $encryptedConnections);
        if ($invalidConnections) {
            throw new \DomainException(sprintf(
                'Try to manage not encrypted connections: %s',
                implode(', ', $invalidConnections),
            ));
        }

        return $connections;
    }
}
