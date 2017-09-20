<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170920110728 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql('CREATE TABLE test_api_year_teacher (year_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_AEFBF92E40C1FEA7 (year_id), INDEX IDX_AEFBF92EA76ED395 (user_id), PRIMARY KEY(year_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE test_api_year_teacher ADD CONSTRAINT FK_AEFBF92E40C1FEA7 FOREIGN KEY (year_id) REFERENCES test_api_year (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_year_teacher ADD CONSTRAINT FK_AEFBF92EA76ED395 FOREIGN KEY (user_id) REFERENCES test_api_user (id) ON DELETE CASCADE');

        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('CREATE TABLE gs_api_year_teacher (year_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1049973A40C1FEA7 (year_id), INDEX IDX_1049973AA76ED395 (user_id), PRIMARY KEY(year_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE gs_api_year_teacher ADD CONSTRAINT FK_1049973A40C1FEA7 FOREIGN KEY (year_id) REFERENCES gs_api_year (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_year_teacher ADD CONSTRAINT FK_1049973AA76ED395 FOREIGN KEY (user_id) REFERENCES gs_api_user (id) ON DELETE CASCADE');
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
            $this->addSql('DROP TABLE test_api_year_teacher');
        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('DROP TABLE gs_api_year_teacher');
        }
    }
}
