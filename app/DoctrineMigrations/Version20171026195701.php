<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171026195701 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql('ALTER TABLE test_api_category ADD can_be_free_topic_for_teachers TINYINT(1) NOT NULL');
            $this->addSql('ALTER TABLE test_api_year ADD number_of_free_topic_per_teacher INT NOT NULL');
        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_category ADD can_be_free_topic_for_teachers TINYINT(1) NOT NULL');
            $this->addSql('ALTER TABLE gs_api_year ADD number_of_free_topic_per_teacher INT NOT NULL');
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
            $this->addSql('ALTER TABLE test_api_category DROP can_be_free_topic_for_teachers');
            $this->addSql('ALTER TABLE test_api_year DROP number_of_free_topic_per_teacher');
        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_category DROP can_be_free_topic_for_teachers');
            $this->addSql('ALTER TABLE gs_api_year DROP number_of_free_topic_per_teacher');
        }
    }
}
