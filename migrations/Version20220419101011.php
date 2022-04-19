<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220419101011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D338D5835E237E06 ON equipment (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9E6EA9495E237E06 ON meeting_room (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_D338D5835E237E06 ON equipment');
        $this->addSql('DROP INDEX UNIQ_9E6EA9495E237E06 ON meeting_room');
    }
}
