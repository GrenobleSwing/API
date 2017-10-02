<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171002065554 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql('ALTER TABLE test_api_account DROP student, DROP unemployed');
            $this->addSql('ALTER TABLE test_api_certificate DROP FOREIGN KEY FK_3525608140C1FEA7');
            $this->addSql('DROP INDEX IDX_3525608140C1FEA7 ON test_api_certificate');
            $this->addSql('ALTER TABLE test_api_certificate DROP year_id');
            $this->addSql('ALTER TABLE test_api_registration ADD semester TINYINT(1) NOT NULL');

        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_account DROP student, DROP unemployed');
            $this->addSql('ALTER TABLE gs_api_certificate DROP FOREIGN KEY FK_8C5B64F340C1FEA7');
            $this->addSql('DROP INDEX IDX_8C5B64F340C1FEA7 ON gs_api_certificate');
            $this->addSql('ALTER TABLE gs_api_certificate DROP year_id');
            $this->addSql('ALTER TABLE gs_api_registration ADD semester TINYINT(1) NOT NULL');
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
            $this->addSql('ALTER TABLE test_api_account ADD student TINYINT(1) NOT NULL, ADD unemployed TINYINT(1) NOT NULL');
            $this->addSql('ALTER TABLE test_api_certificate ADD year_id INT NOT NULL');
            $this->addSql('ALTER TABLE test_api_certificate ADD CONSTRAINT FK_3525608140C1FEA7 FOREIGN KEY (year_id) REFERENCES test_api_year (id)');
            $this->addSql('CREATE INDEX IDX_3525608140C1FEA7 ON test_api_certificate (year_id)');
            $this->addSql('ALTER TABLE test_api_registration DROP semester');

        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_account ADD student TINYINT(1) NOT NULL, ADD unemployed TINYINT(1) NOT NULL');
            $this->addSql('ALTER TABLE gs_api_certificate ADD year_id INT NOT NULL');
            $this->addSql('ALTER TABLE gs_api_certificate ADD CONSTRAINT FK_8C5B64F340C1FEA7 FOREIGN KEY (year_id) REFERENCES gs_api_year (id)');
            $this->addSql('CREATE INDEX IDX_8C5B64F340C1FEA7 ON gs_api_certificate (year_id)');
            $this->addSql('ALTER TABLE gs_api_registration DROP semester');
        }
    }
}
