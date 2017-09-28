<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170928051818 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        if ( $this->container->get('kernel')->getEnvironment() == "dev" ) {
            $this->addSql('ALTER TABLE test_api_certificate ADD updated_at DATETIME NOT NULL, ADD file_name VARCHAR(255) DEFAULT NULL');
        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_certificate ADD updated_at DATETIME NOT NULL, ADD file_name VARCHAR(255) DEFAULT NULL');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        if ( $this->container->get('kernel')->getEnvironment() == "dev" ) {
            $this->addSql('ALTER TABLE test_api_certificate DROP updated_at, DROP file_name');
        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_certificate DROP updated_at, DROP file_name');
        }
    }
}
