<?php

namespace SourceCroc\Migrations\AccessControlBundle;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\MigrationException;

class Version20220131150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SourceCroc] Add table to support single use tokens';
    }

    /**
     * @inheritDoc
     */
    public function up(Schema $schema): void
    {
        $refreshTable = $schema->createTable('`sourcecroc/access-control/used_tokens`');
        $refreshTable->addColumn('id', Types::INTEGER, ['unsigned' => true, 'autoincrement' => true]);
        $refreshTable->addColumn('token', Types::STRING, ['length' => 511]);
        $refreshTable->addColumn('refresh', Types::STRING, ['length' => 511]);
        $refreshTable->addColumn('used_on', Types::DATETIME_IMMUTABLE);
        $refreshTable->addColumn('expires_on', Types::DATETIME_IMMUTABLE);
        $refreshTable->setPrimaryKey(['id'], 'PIX_TOKENS');
        $refreshTable->addUniqueIndex(['token'],'UIX_TOKEN');
        $refreshTable->addUniqueIndex(['refresh'],'UIX_REFRESH');
        $refreshTable->addIndex(['expires_on'], 'IDX_EON');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('`sourcecroc/access-control/used_tokens');
    }
}