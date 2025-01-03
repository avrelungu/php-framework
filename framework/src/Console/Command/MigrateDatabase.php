<?php

namespace AurelLungu\Framework\Console\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

class MigrateDatabase implements CommandInterface
{
    public string $name = 'database:migrations:migrate';

    public function __construct(
        private Connection $connection,
        private string $migrationFilesPath
    ) {

    }

    public function execute(array $params = []): int
    {
        try {
            // Create a migrations table SQL if table not already in existence
            $this->createMigrationTable();

            $this->connection->beginTransaction();

            // Get $appliedMigrations which are already in the database.migrations table
            $appliedMigrations = $this->getAppliedMigrations();

            // Get the $migrationFiles from the migrations folder
            $migrationFiles = $this->getMigrationFiles();

            // Get the migrations to apply. i.e. they are in $migrationFiles but not in $appliedMigrations
            $migrationToExecute = array_diff($migrationFiles, $appliedMigrations);

            if (!$migrationToExecute) {
                echo 'no migrations to execute' . PHP_EOL;

                return 0;
            }

            // Create SQL for any migrations which have not been run ..i.e. which are not in the database
            $schema = new Schema();

            foreach ($migrationToExecute as $migration) {
                $migrationClass = require $this->migrationFilesPath . $migration;

                $migrationClass->up($schema);

                // Add migration to database
                $this->insertMigration($migration);
            }
            // Execute the SQL query
            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

            foreach ($sqlArray as $sql) {
                $this->connection->executeQuery($sql);
            }

            $this->connection->commit();

            return 0;
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            throw $exception;
        }
    }

    private function insertMigration(string $migration): void
    {
        $sql = "INSERT INTO migrations (migration) VALUES (?)";

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(1, $migration);

        $stmt->executeStatement();
    }

    private function getMigrationFiles(): array 
    {
        $migrationFiles = scandir($this->migrationFilesPath);

        $migrations = array_filter($migrationFiles, fn($migrationFile) => !in_array($migrationFile, ['..', '.']));

        return $migrations;
    }

    private function getAppliedMigrations()
    {
        $sql = "SELECT migration FROM migrations";

        $appliedMigrations = $this->connection->executeQuery($sql)->fetchFirstColumn();

        return $appliedMigrations;
    }

    private function createMigrationTable(): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist('migrations')) {
            $schema = new Schema();

            $table = $schema->createTable('migrations');
            $table->addColumn('id', Types::INTEGER, ['unsigned' => true, 'autoincrement' => true]);
            $table->addColumn('migration', Types::STRING);
            $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['default' => 'CURRENT_TIMESTAMP']);
            $table->setPrimaryKey(['id']);

            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

            $this->connection->executeQuery($sqlArray[0]);

            echo '`migrations` table was created.';
        }
    }
}
