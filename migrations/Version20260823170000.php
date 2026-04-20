<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260823170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ORM users and allow production-length movie descriptions and image URLs.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_88BDF3E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movie CHANGE description description LONGTEXT DEFAULT NULL, CHANGE image_path image_path VARCHAR(500) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE app_user');
        $this->addSql('ALTER TABLE movie CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE image_path image_path VARCHAR(255) NOT NULL');
    }
}
