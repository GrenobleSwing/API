<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use GS\ETransactionBundle\Entity\Config;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170917212608 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $prefix = $this->container->getParameter('gsapi_doctrine_table_prefix');

        $this->addSql('CREATE TABLE ' . $prefix . 'gs_etran_config (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, site VARCHAR(7) DEFAULT NULL, rang VARCHAR(2) DEFAULT NULL, identifiant VARCHAR(9) DEFAULT NULL, devise VARCHAR(3) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ' . $prefix . 'gs_etran_environment (id INT AUTO_INCREMENT NOT NULL, config_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, hmac_key VARCHAR(255) NOT NULL, url_classique VARCHAR(255) NOT NULL, url_light VARCHAR(255) DEFAULT NULL, url_mobile VARCHAR(255) NOT NULL, valid_ips LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_8C1D42AB24DB0683 (config_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ' . $prefix . 'gs_etran_payment (id INT AUTO_INCREMENT NOT NULL, environment_id INT DEFAULT NULL, total VARCHAR(255) NOT NULL, cmd VARCHAR(255) NOT NULL, porteur VARCHAR(255) NOT NULL, time DATETIME NOT NULL, ipn_url VARCHAR(255) NOT NULL, url_effectue VARCHAR(255) NOT NULL, url_refuse VARCHAR(255) NOT NULL, url_annule VARCHAR(255) NOT NULL, INDEX IDX_28E2372903E3A94 (environment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ' . $prefix . 'gs_etran_environment ADD CONSTRAINT FK_8C1D42AB24DB0683 FOREIGN KEY (config_id) REFERENCES ' . $prefix . 'gs_etran_config (id)');
        $this->addSql('ALTER TABLE ' . $prefix . 'gs_etran_payment ADD CONSTRAINT FK_28E2372903E3A94 FOREIGN KEY (environment_id) REFERENCES ' . $prefix . 'gs_etran_environment (id)');
        $this->addSql('ALTER TABLE ' . $prefix . 'account ADD unemployed TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ' . $prefix . 'payment ADD ref VARCHAR(23) DEFAULT NULL, DROP paypal_payment_id');
        $this->addSql('ALTER TABLE ' . $prefix . 'society ADD payment_config_id INT DEFAULT NULL, ADD payment_environment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ' . $prefix . 'society ADD CONSTRAINT FK_BF24AE2B67F229AB FOREIGN KEY (payment_config_id) REFERENCES ' . $prefix . 'gs_etran_config (id)');
        $this->addSql('ALTER TABLE ' . $prefix . 'society ADD CONSTRAINT FK_BF24AE2B75168090 FOREIGN KEY (payment_environment_id) REFERENCES ' . $prefix . 'gs_etran_environment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF24AE2B67F229AB ON ' . $prefix . 'society (payment_config_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF24AE2B75168090 ON ' . $prefix . 'society (payment_environment_id)');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $societies = $em
            ->getRepository('GSApiBundle:Society')
            ->findAll()
            ;

        foreach ($societies as $society) {
            $society->setPaymentConfig(new Config());
        }

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $prefix = $this->container->getParameter('gsapi_doctrine_table_prefix');

        $this->addSql('ALTER TABLE ' . $prefix . 'society DROP FOREIGN KEY FK_BF24AE2B67F229AB');
        $this->addSql('ALTER TABLE ' . $prefix . 'gs_etran_environment DROP FOREIGN KEY FK_8C1D42AB24DB0683');
        $this->addSql('ALTER TABLE ' . $prefix . 'society DROP FOREIGN KEY FK_BF24AE2B75168090');
        $this->addSql('ALTER TABLE ' . $prefix . 'gs_etran_payment DROP FOREIGN KEY FK_28E2372903E3A94');
        $this->addSql('DROP TABLE ' . $prefix . 'gs_etran_config');
        $this->addSql('DROP TABLE ' . $prefix . 'gs_etran_environment');
        $this->addSql('DROP TABLE ' . $prefix . 'gs_etran_payment');
        $this->addSql('ALTER TABLE ' . $prefix . 'account DROP unemployed');
        $this->addSql('ALTER TABLE ' . $prefix . 'payment ADD paypal_payment_id VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, DROP ref');
        $this->addSql('DROP INDEX UNIQ_BF24AE2B67F229AB ON ' . $prefix . 'society');
        $this->addSql('DROP INDEX UNIQ_BF24AE2B75168090 ON ' . $prefix . 'society');
        $this->addSql('ALTER TABLE ' . $prefix . 'society DROP payment_config_id, DROP payment_environment_id');
    }
}
