<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170926023549 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql('CREATE TABLE test_api_certificate (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, year_id INT NOT NULL, type VARCHAR(12) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, INDEX IDX_352560819B6B5FBA (account_id), INDEX IDX_3525608140C1FEA7 (year_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE test_api_certificate ADD CONSTRAINT FK_352560819B6B5FBA FOREIGN KEY (account_id) REFERENCES test_api_account (id)');
            $this->addSql('ALTER TABLE test_api_certificate ADD CONSTRAINT FK_3525608140C1FEA7 FOREIGN KEY (year_id) REFERENCES test_api_year (id)');

        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('CREATE TABLE gs_api_certificate (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, year_id INT NOT NULL, type VARCHAR(12) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, INDEX IDX_8C5B64F39B6B5FBA (account_id), INDEX IDX_8C5B64F340C1FEA7 (year_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE gs_api_certificate ADD CONSTRAINT FK_8C5B64F39B6B5FBA FOREIGN KEY (account_id) REFERENCES gs_api_account (id)');
            $this->addSql('ALTER TABLE gs_api_certificate ADD CONSTRAINT FK_8C5B64F340C1FEA7 FOREIGN KEY (year_id) REFERENCES gs_api_year (id)');
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
            $this->addSql('DROP TABLE test_api_certificate');
        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('DROP TABLE gs_api_certificate');
        }
    }
}
