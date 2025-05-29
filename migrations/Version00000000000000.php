<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version00000000000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renames order table to customer_order to avoid SQL keyword conflict';
    }

    public function up(Schema $schema): void
    {
        // THIS IS THE IMPORTANT PART - the rename operation
        $this->addSql('RENAME TABLE `order` TO customer_order');
    }

    public function down(Schema $schema): void
    {
        // This allows rolling back the migration
        $this->addSql('RENAME TABLE customer_order TO `order`');
    }
}