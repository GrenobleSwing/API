<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170915061649 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql('CREATE TABLE test_api_lexik_email (id INT AUTO_INCREMENT NOT NULL, layout_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, bcc VARCHAR(255) DEFAULT NULL, spool TINYINT(1) NOT NULL, headers LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', use_fallback_locale TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_C33833E5AEA34913 (reference), INDEX IDX_C33833E58C22AA1A (layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_lexik_email_translation (id INT AUTO_INCREMENT NOT NULL, email_id INT DEFAULT NULL, lang VARCHAR(5) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, body_text LONGTEXT DEFAULT NULL, from_address VARCHAR(255) DEFAULT NULL, from_name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_71045335A832C1C9 (email_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_lexik_layout (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, default_locale VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_BD16627FAEA34913 (reference), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_lexik_layout_translation (id INT AUTO_INCREMENT NOT NULL, layout_id INT DEFAULT NULL, lang VARCHAR(5) NOT NULL, body LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_272910888C22AA1A (layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_account (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(64) NOT NULL, last_name VARCHAR(64) NOT NULL, birth_date DATE NOT NULL, phone_number VARCHAR(35) NOT NULL COMMENT \'(DC2Type:phone_number)\', student TINYINT(1) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_CF76997DE7927C74 (email), UNIQUE INDEX UNIQ_CF76997DF5B7AF75 (address_id), UNIQUE INDEX UNIQ_CF76997DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_activity (id INT AUTO_INCREMENT NOT NULL, email_layout_id INT DEFAULT NULL, membership_topic_id INT DEFAULT NULL, year_id INT NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, state VARCHAR(16) NOT NULL, membership TINYINT(1) NOT NULL, members_only TINYINT(1) NOT NULL, triggered_emails LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_53C923E583293531 (email_layout_id), INDEX IDX_53C923E53B0ECC9 (membership_topic_id), INDEX IDX_53C923E540C1FEA7 (year_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_activity_user (activity_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8DED4D7581C06096 (activity_id), INDEX IDX_8DED4D75A76ED395 (user_id), PRIMARY KEY(activity_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_activity_email (id INT AUTO_INCREMENT NOT NULL, email_template_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, action VARCHAR(16) NOT NULL, INDEX IDX_C8FD7E68131A730F (email_template_id), INDEX IDX_C8FD7E6881C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_address (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, county VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_category (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, name VARCHAR(200) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_F9F1337E81C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_category_discount (category_id INT NOT NULL, discount_id INT NOT NULL, INDEX IDX_C97C40B12469DE2 (category_id), INDEX IDX_C97C40B4C7C611F (discount_id), PRIMARY KEY(category_id, discount_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_discount (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, name VARCHAR(200) NOT NULL, type VARCHAR(20) NOT NULL, value DOUBLE PRECISION NOT NULL, `condition` VARCHAR(200) NOT NULL, INDEX IDX_1E5D9EB181C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_invoice (id INT AUTO_INCREMENT NOT NULL, payment_id INT DEFAULT NULL, number VARCHAR(50) NOT NULL, date DATE NOT NULL, UNIQUE INDEX UNIQ_2225D89D96901F54 (number), UNIQUE INDEX UNIQ_2225D89D4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_payment (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, type VARCHAR(10) NOT NULL, state VARCHAR(6) NOT NULL, comment LONGTEXT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, date DATE NOT NULL, paypal_payment_id VARCHAR(64) DEFAULT NULL, INDEX IDX_DF684BD49B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_payment_item (id INT AUTO_INCREMENT NOT NULL, registration_id INT NOT NULL, discount_id INT DEFAULT NULL, payment_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_323A10E8833D8F43 (registration_id), INDEX IDX_323A10E84C7C611F (discount_id), INDEX IDX_323A10E84C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_registration (id INT AUTO_INCREMENT NOT NULL, topic_id INT NOT NULL, account_id INT NOT NULL, partner_registration_id INT DEFAULT NULL, created_at DATETIME NOT NULL, role VARCHAR(16) DEFAULT NULL, with_partner TINYINT(1) NOT NULL, partner_email VARCHAR(255) DEFAULT NULL, partner_first_name VARCHAR(64) DEFAULT NULL, partner_last_name VARCHAR(64) DEFAULT NULL, state VARCHAR(20) NOT NULL, amount_paid DOUBLE PRECISION NOT NULL, accept_rules TINYINT(1) NOT NULL, INDEX IDX_6E0A05251F55203D (topic_id), INDEX IDX_6E0A05259B6B5FBA (account_id), INDEX IDX_6E0A05253237F1EE (partner_registration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_schedule (id INT AUTO_INCREMENT NOT NULL, venue_id INT DEFAULT NULL, topic_id INT NOT NULL, start_date DATE NOT NULL, start_time TIME NOT NULL, end_date DATE NOT NULL, end_time TIME NOT NULL, frequency VARCHAR(20) NOT NULL, teachers VARCHAR(100) DEFAULT NULL, INDEX IDX_A5853B4440A73EBA (venue_id), INDEX IDX_A5853B441F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_society (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, email_payment_layout_id INT DEFAULT NULL, email_payment_template_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, tax_information LONGTEXT NOT NULL, vat_information LONGTEXT NOT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(35) NOT NULL COMMENT \'(DC2Type:phone_number)\', UNIQUE INDEX UNIQ_BF24AE2BE7927C74 (email), UNIQUE INDEX UNIQ_BF24AE2BF5B7AF75 (address_id), UNIQUE INDEX UNIQ_BF24AE2B9A009AF5 (email_payment_layout_id), UNIQUE INDEX UNIQ_BF24AE2BFAE5C615 (email_payment_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_topic (id INT AUTO_INCREMENT NOT NULL, activity_id INT DEFAULT NULL, category_id INT NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(16) NOT NULL, state VARCHAR(16) NOT NULL, auto_validation TINYINT(1) NOT NULL, options LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_3B481D0181C06096 (activity_id), INDEX IDX_3B481D0112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_topic_requirements (topic_source INT NOT NULL, topic_target INT NOT NULL, INDEX IDX_995C77F1FC8D4CAB (topic_source), INDEX IDX_995C77F1E5681C24 (topic_target), PRIMARY KEY(topic_source, topic_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_topic_owner (topic_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_60EAF0D51F55203D (topic_id), INDEX IDX_60EAF0D5A76ED395 (user_id), PRIMARY KEY(topic_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_topic_moderator (topic_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C5FDF59D1F55203D (topic_id), INDEX IDX_C5FDF59DA76ED395 (user_id), PRIMARY KEY(topic_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', hash VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_3907ABEC92FC23A8 (username_canonical), UNIQUE INDEX UNIQ_3907ABECA0D96FBF (email_canonical), UNIQUE INDEX UNIQ_3907ABECC05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_venue (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3799D817F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_year (id INT AUTO_INCREMENT NOT NULL, society_id INT NOT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, state VARCHAR(16) NOT NULL, INDEX IDX_F160E92E6389D24 (society_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE test_api_year_user (year_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_49C661C40C1FEA7 (year_id), INDEX IDX_49C661CA76ED395 (user_id), PRIMARY KEY(year_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE test_api_lexik_email ADD CONSTRAINT FK_C33833E58C22AA1A FOREIGN KEY (layout_id) REFERENCES test_api_lexik_layout (id)');
            $this->addSql('ALTER TABLE test_api_lexik_email_translation ADD CONSTRAINT FK_71045335A832C1C9 FOREIGN KEY (email_id) REFERENCES test_api_lexik_email (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_lexik_layout_translation ADD CONSTRAINT FK_272910888C22AA1A FOREIGN KEY (layout_id) REFERENCES test_api_lexik_layout (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_account ADD CONSTRAINT FK_CF76997DF5B7AF75 FOREIGN KEY (address_id) REFERENCES test_api_address (id)');
            $this->addSql('ALTER TABLE test_api_account ADD CONSTRAINT FK_CF76997DA76ED395 FOREIGN KEY (user_id) REFERENCES test_api_user (id)');
            $this->addSql('ALTER TABLE test_api_activity ADD CONSTRAINT FK_53C923E583293531 FOREIGN KEY (email_layout_id) REFERENCES test_api_lexik_layout (id)');
            $this->addSql('ALTER TABLE test_api_activity ADD CONSTRAINT FK_53C923E53B0ECC9 FOREIGN KEY (membership_topic_id) REFERENCES test_api_topic (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE test_api_activity ADD CONSTRAINT FK_53C923E540C1FEA7 FOREIGN KEY (year_id) REFERENCES test_api_year (id)');
            $this->addSql('ALTER TABLE test_api_activity_user ADD CONSTRAINT FK_8DED4D7581C06096 FOREIGN KEY (activity_id) REFERENCES test_api_activity (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_activity_user ADD CONSTRAINT FK_8DED4D75A76ED395 FOREIGN KEY (user_id) REFERENCES test_api_user (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_activity_email ADD CONSTRAINT FK_C8FD7E68131A730F FOREIGN KEY (email_template_id) REFERENCES test_api_lexik_email (id)');
            $this->addSql('ALTER TABLE test_api_activity_email ADD CONSTRAINT FK_C8FD7E6881C06096 FOREIGN KEY (activity_id) REFERENCES test_api_activity (id)');
            $this->addSql('ALTER TABLE test_api_category ADD CONSTRAINT FK_F9F1337E81C06096 FOREIGN KEY (activity_id) REFERENCES test_api_activity (id)');
            $this->addSql('ALTER TABLE test_api_category_discount ADD CONSTRAINT FK_C97C40B12469DE2 FOREIGN KEY (category_id) REFERENCES test_api_category (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_category_discount ADD CONSTRAINT FK_C97C40B4C7C611F FOREIGN KEY (discount_id) REFERENCES test_api_discount (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_discount ADD CONSTRAINT FK_1E5D9EB181C06096 FOREIGN KEY (activity_id) REFERENCES test_api_activity (id)');
            $this->addSql('ALTER TABLE test_api_invoice ADD CONSTRAINT FK_2225D89D4C3A3BB FOREIGN KEY (payment_id) REFERENCES test_api_payment (id)');
            $this->addSql('ALTER TABLE test_api_payment ADD CONSTRAINT FK_DF684BD49B6B5FBA FOREIGN KEY (account_id) REFERENCES test_api_account (id)');
            $this->addSql('ALTER TABLE test_api_payment_item ADD CONSTRAINT FK_323A10E8833D8F43 FOREIGN KEY (registration_id) REFERENCES test_api_registration (id)');
            $this->addSql('ALTER TABLE test_api_payment_item ADD CONSTRAINT FK_323A10E84C7C611F FOREIGN KEY (discount_id) REFERENCES test_api_discount (id)');
            $this->addSql('ALTER TABLE test_api_payment_item ADD CONSTRAINT FK_323A10E84C3A3BB FOREIGN KEY (payment_id) REFERENCES test_api_payment (id)');
            $this->addSql('ALTER TABLE test_api_registration ADD CONSTRAINT FK_6E0A05251F55203D FOREIGN KEY (topic_id) REFERENCES test_api_topic (id)');
            $this->addSql('ALTER TABLE test_api_registration ADD CONSTRAINT FK_6E0A05259B6B5FBA FOREIGN KEY (account_id) REFERENCES test_api_account (id)');
            $this->addSql('ALTER TABLE test_api_registration ADD CONSTRAINT FK_6E0A05253237F1EE FOREIGN KEY (partner_registration_id) REFERENCES test_api_registration (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE test_api_schedule ADD CONSTRAINT FK_A5853B4440A73EBA FOREIGN KEY (venue_id) REFERENCES test_api_venue (id)');
            $this->addSql('ALTER TABLE test_api_schedule ADD CONSTRAINT FK_A5853B441F55203D FOREIGN KEY (topic_id) REFERENCES test_api_topic (id)');
            $this->addSql('ALTER TABLE test_api_society ADD CONSTRAINT FK_BF24AE2BF5B7AF75 FOREIGN KEY (address_id) REFERENCES test_api_address (id)');
            $this->addSql('ALTER TABLE test_api_society ADD CONSTRAINT FK_BF24AE2B9A009AF5 FOREIGN KEY (email_payment_layout_id) REFERENCES test_api_lexik_layout (id)');
            $this->addSql('ALTER TABLE test_api_society ADD CONSTRAINT FK_BF24AE2BFAE5C615 FOREIGN KEY (email_payment_template_id) REFERENCES test_api_lexik_email (id)');
            $this->addSql('ALTER TABLE test_api_topic ADD CONSTRAINT FK_3B481D0181C06096 FOREIGN KEY (activity_id) REFERENCES test_api_activity (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE test_api_topic ADD CONSTRAINT FK_3B481D0112469DE2 FOREIGN KEY (category_id) REFERENCES test_api_category (id)');
            $this->addSql('ALTER TABLE test_api_topic_requirements ADD CONSTRAINT FK_995C77F1FC8D4CAB FOREIGN KEY (topic_source) REFERENCES test_api_topic (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_topic_requirements ADD CONSTRAINT FK_995C77F1E5681C24 FOREIGN KEY (topic_target) REFERENCES test_api_topic (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_topic_owner ADD CONSTRAINT FK_60EAF0D51F55203D FOREIGN KEY (topic_id) REFERENCES test_api_topic (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_topic_owner ADD CONSTRAINT FK_60EAF0D5A76ED395 FOREIGN KEY (user_id) REFERENCES test_api_user (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_topic_moderator ADD CONSTRAINT FK_C5FDF59D1F55203D FOREIGN KEY (topic_id) REFERENCES test_api_topic (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_topic_moderator ADD CONSTRAINT FK_C5FDF59DA76ED395 FOREIGN KEY (user_id) REFERENCES test_api_user (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_venue ADD CONSTRAINT FK_3799D817F5B7AF75 FOREIGN KEY (address_id) REFERENCES test_api_address (id)');
            $this->addSql('ALTER TABLE test_api_year ADD CONSTRAINT FK_F160E92E6389D24 FOREIGN KEY (society_id) REFERENCES test_api_society (id)');
            $this->addSql('ALTER TABLE test_api_year_user ADD CONSTRAINT FK_49C661C40C1FEA7 FOREIGN KEY (year_id) REFERENCES test_api_year (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE test_api_year_user ADD CONSTRAINT FK_49C661CA76ED395 FOREIGN KEY (user_id) REFERENCES test_api_user (id) ON DELETE CASCADE');

        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('CREATE TABLE gs_api_lexik_email (id INT AUTO_INCREMENT NOT NULL, layout_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, bcc VARCHAR(255) DEFAULT NULL, spool TINYINT(1) NOT NULL, headers LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', use_fallback_locale TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_7A463797AEA34913 (reference), INDEX IDX_7A4637978C22AA1A (layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_lexik_email_translation (id INT AUTO_INCREMENT NOT NULL, email_id INT DEFAULT NULL, lang VARCHAR(5) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, body_text LONGTEXT DEFAULT NULL, from_address VARCHAR(255) DEFAULT NULL, from_name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5EBFC31A832C1C9 (email_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_lexik_layout (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, default_locale VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3A40C6BAEA34913 (reference), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_lexik_layout_translation (id INT AUTO_INCREMENT NOT NULL, layout_id INT DEFAULT NULL, lang VARCHAR(5) NOT NULL, body LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_20303B3E8C22AA1A (layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_account (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(64) NOT NULL, last_name VARCHAR(64) NOT NULL, birth_date DATE NOT NULL, phone_number VARCHAR(35) NOT NULL COMMENT \'(DC2Type:phone_number)\', student TINYINT(1) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8AF51A36E7927C74 (email), UNIQUE INDEX UNIQ_8AF51A36F5B7AF75 (address_id), UNIQUE INDEX UNIQ_8AF51A36A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_activity (id INT AUTO_INCREMENT NOT NULL, email_layout_id INT DEFAULT NULL, membership_topic_id INT DEFAULT NULL, year_id INT NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, state VARCHAR(16) NOT NULL, membership TINYINT(1) NOT NULL, members_only TINYINT(1) NOT NULL, triggered_emails LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_B282387E83293531 (email_layout_id), INDEX IDX_B282387E3B0ECC9 (membership_topic_id), INDEX IDX_B282387E40C1FEA7 (year_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_activity_user (activity_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_97892B6681C06096 (activity_id), INDEX IDX_97892B66A76ED395 (user_id), PRIMARY KEY(activity_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_activity_email (id INT AUTO_INCREMENT NOT NULL, email_template_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, action VARCHAR(16) NOT NULL, INDEX IDX_4C595BD0131A730F (email_template_id), INDEX IDX_4C595BD081C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_address (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, county VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_category (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, name VARCHAR(200) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_18BA28E581C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_category_discount (category_id INT NOT NULL, discount_id INT NOT NULL, INDEX IDX_FE92DC3112469DE2 (category_id), INDEX IDX_FE92DC314C7C611F (discount_id), PRIMARY KEY(category_id, discount_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_discount (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, name VARCHAR(200) NOT NULL, type VARCHAR(20) NOT NULL, value DOUBLE PRECISION NOT NULL, `condition` VARCHAR(200) NOT NULL, INDEX IDX_FF16852A81C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_invoice (id INT AUTO_INCREMENT NOT NULL, payment_id INT DEFAULT NULL, number VARCHAR(50) NOT NULL, date DATE NOT NULL, UNIQUE INDEX UNIQ_67A65BD696901F54 (number), UNIQUE INDEX UNIQ_67A65BD64C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_payment (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, type VARCHAR(10) NOT NULL, state VARCHAR(6) NOT NULL, comment LONGTEXT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, date DATE NOT NULL, paypal_payment_id VARCHAR(64) DEFAULT NULL, INDEX IDX_9AEBC89F9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_payment_item (id INT AUTO_INCREMENT NOT NULL, registration_id INT NOT NULL, discount_id INT DEFAULT NULL, payment_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_8C887EFC833D8F43 (registration_id), INDEX IDX_8C887EFC4C7C611F (discount_id), INDEX IDX_8C887EFC4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_registration (id INT AUTO_INCREMENT NOT NULL, topic_id INT NOT NULL, account_id INT NOT NULL, partner_registration_id INT DEFAULT NULL, created_at DATETIME NOT NULL, role VARCHAR(16) DEFAULT NULL, with_partner TINYINT(1) NOT NULL, partner_email VARCHAR(255) DEFAULT NULL, partner_first_name VARCHAR(64) DEFAULT NULL, partner_last_name VARCHAR(64) DEFAULT NULL, state VARCHAR(20) NOT NULL, amount_paid DOUBLE PRECISION NOT NULL, accept_rules TINYINT(1) NOT NULL, INDEX IDX_D0B86B311F55203D (topic_id), INDEX IDX_D0B86B319B6B5FBA (account_id), INDEX IDX_D0B86B313237F1EE (partner_registration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_schedule (id INT AUTO_INCREMENT NOT NULL, venue_id INT DEFAULT NULL, topic_id INT NOT NULL, start_date DATE NOT NULL, start_time TIME NOT NULL, end_date DATE NOT NULL, end_time TIME NOT NULL, frequency VARCHAR(20) NOT NULL, teachers VARCHAR(100) DEFAULT NULL, INDEX IDX_44CE20DF40A73EBA (venue_id), INDEX IDX_44CE20DF1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_society (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, email_payment_layout_id INT DEFAULT NULL, email_payment_template_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, tax_information LONGTEXT NOT NULL, vat_information LONGTEXT NOT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(35) NOT NULL COMMENT \'(DC2Type:phone_number)\', UNIQUE INDEX UNIQ_FAA72D60E7927C74 (email), UNIQUE INDEX UNIQ_FAA72D60F5B7AF75 (address_id), UNIQUE INDEX UNIQ_FAA72D609A009AF5 (email_payment_layout_id), UNIQUE INDEX UNIQ_FAA72D60FAE5C615 (email_payment_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_topic (id INT AUTO_INCREMENT NOT NULL, activity_id INT DEFAULT NULL, category_id INT NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(16) NOT NULL, state VARCHAR(16) NOT NULL, auto_validation TINYINT(1) NOT NULL, options LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_571C34BA81C06096 (activity_id), INDEX IDX_571C34BA12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_topic_requirements (topic_source INT NOT NULL, topic_target INT NOT NULL, INDEX IDX_5FA2AB5BFC8D4CAB (topic_source), INDEX IDX_5FA2AB5BE5681C24 (topic_target), PRIMARY KEY(topic_source, topic_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_topic_owner (topic_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D994F4A71F55203D (topic_id), INDEX IDX_D994F4A7A76ED395 (user_id), PRIMARY KEY(topic_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_topic_moderator (topic_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C36A061F55203D (topic_id), INDEX IDX_C36A06A76ED395 (user_id), PRIMARY KEY(topic_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', hash VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_6BBBFDB892FC23A8 (username_canonical), UNIQUE INDEX UNIQ_6BBBFDB8A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_6BBBFDB8C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_venue (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5BCDF1ACF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_year (id INT AUTO_INCREMENT NOT NULL, society_id INT NOT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, state VARCHAR(16) NOT NULL, INDEX IDX_5DAA58C6E6389D24 (society_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE gs_api_year_user (year_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_63A067CB40C1FEA7 (year_id), INDEX IDX_63A067CBA76ED395 (user_id), PRIMARY KEY(year_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE gs_api_lexik_email ADD CONSTRAINT FK_7A4637978C22AA1A FOREIGN KEY (layout_id) REFERENCES gs_api_lexik_layout (id)');
            $this->addSql('ALTER TABLE gs_api_lexik_email_translation ADD CONSTRAINT FK_5EBFC31A832C1C9 FOREIGN KEY (email_id) REFERENCES gs_api_lexik_email (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_lexik_layout_translation ADD CONSTRAINT FK_20303B3E8C22AA1A FOREIGN KEY (layout_id) REFERENCES gs_api_lexik_layout (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_account ADD CONSTRAINT FK_8AF51A36F5B7AF75 FOREIGN KEY (address_id) REFERENCES gs_api_address (id)');
            $this->addSql('ALTER TABLE gs_api_account ADD CONSTRAINT FK_8AF51A36A76ED395 FOREIGN KEY (user_id) REFERENCES gs_api_user (id)');
            $this->addSql('ALTER TABLE gs_api_activity ADD CONSTRAINT FK_B282387E83293531 FOREIGN KEY (email_layout_id) REFERENCES gs_api_lexik_layout (id)');
            $this->addSql('ALTER TABLE gs_api_activity ADD CONSTRAINT FK_B282387E3B0ECC9 FOREIGN KEY (membership_topic_id) REFERENCES gs_api_topic (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE gs_api_activity ADD CONSTRAINT FK_B282387E40C1FEA7 FOREIGN KEY (year_id) REFERENCES gs_api_year (id)');
            $this->addSql('ALTER TABLE gs_api_activity_user ADD CONSTRAINT FK_97892B6681C06096 FOREIGN KEY (activity_id) REFERENCES gs_api_activity (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_activity_user ADD CONSTRAINT FK_97892B66A76ED395 FOREIGN KEY (user_id) REFERENCES gs_api_user (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_activity_email ADD CONSTRAINT FK_4C595BD0131A730F FOREIGN KEY (email_template_id) REFERENCES gs_api_lexik_email (id)');
            $this->addSql('ALTER TABLE gs_api_activity_email ADD CONSTRAINT FK_4C595BD081C06096 FOREIGN KEY (activity_id) REFERENCES gs_api_activity (id)');
            $this->addSql('ALTER TABLE gs_api_category ADD CONSTRAINT FK_18BA28E581C06096 FOREIGN KEY (activity_id) REFERENCES gs_api_activity (id)');
            $this->addSql('ALTER TABLE gs_api_category_discount ADD CONSTRAINT FK_FE92DC3112469DE2 FOREIGN KEY (category_id) REFERENCES gs_api_category (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_category_discount ADD CONSTRAINT FK_FE92DC314C7C611F FOREIGN KEY (discount_id) REFERENCES gs_api_discount (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_discount ADD CONSTRAINT FK_FF16852A81C06096 FOREIGN KEY (activity_id) REFERENCES gs_api_activity (id)');
            $this->addSql('ALTER TABLE gs_api_invoice ADD CONSTRAINT FK_67A65BD64C3A3BB FOREIGN KEY (payment_id) REFERENCES gs_api_payment (id)');
            $this->addSql('ALTER TABLE gs_api_payment ADD CONSTRAINT FK_9AEBC89F9B6B5FBA FOREIGN KEY (account_id) REFERENCES gs_api_account (id)');
            $this->addSql('ALTER TABLE gs_api_payment_item ADD CONSTRAINT FK_8C887EFC833D8F43 FOREIGN KEY (registration_id) REFERENCES gs_api_registration (id)');
            $this->addSql('ALTER TABLE gs_api_payment_item ADD CONSTRAINT FK_8C887EFC4C7C611F FOREIGN KEY (discount_id) REFERENCES gs_api_discount (id)');
            $this->addSql('ALTER TABLE gs_api_payment_item ADD CONSTRAINT FK_8C887EFC4C3A3BB FOREIGN KEY (payment_id) REFERENCES gs_api_payment (id)');
            $this->addSql('ALTER TABLE gs_api_registration ADD CONSTRAINT FK_D0B86B311F55203D FOREIGN KEY (topic_id) REFERENCES gs_api_topic (id)');
            $this->addSql('ALTER TABLE gs_api_registration ADD CONSTRAINT FK_D0B86B319B6B5FBA FOREIGN KEY (account_id) REFERENCES gs_api_account (id)');
            $this->addSql('ALTER TABLE gs_api_registration ADD CONSTRAINT FK_D0B86B313237F1EE FOREIGN KEY (partner_registration_id) REFERENCES gs_api_registration (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE gs_api_schedule ADD CONSTRAINT FK_44CE20DF40A73EBA FOREIGN KEY (venue_id) REFERENCES gs_api_venue (id)');
            $this->addSql('ALTER TABLE gs_api_schedule ADD CONSTRAINT FK_44CE20DF1F55203D FOREIGN KEY (topic_id) REFERENCES gs_api_topic (id)');
            $this->addSql('ALTER TABLE gs_api_society ADD CONSTRAINT FK_FAA72D60F5B7AF75 FOREIGN KEY (address_id) REFERENCES gs_api_address (id)');
            $this->addSql('ALTER TABLE gs_api_society ADD CONSTRAINT FK_FAA72D609A009AF5 FOREIGN KEY (email_payment_layout_id) REFERENCES gs_api_lexik_layout (id)');
            $this->addSql('ALTER TABLE gs_api_society ADD CONSTRAINT FK_FAA72D60FAE5C615 FOREIGN KEY (email_payment_template_id) REFERENCES gs_api_lexik_email (id)');
            $this->addSql('ALTER TABLE gs_api_topic ADD CONSTRAINT FK_571C34BA81C06096 FOREIGN KEY (activity_id) REFERENCES gs_api_activity (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE gs_api_topic ADD CONSTRAINT FK_571C34BA12469DE2 FOREIGN KEY (category_id) REFERENCES gs_api_category (id)');
            $this->addSql('ALTER TABLE gs_api_topic_requirements ADD CONSTRAINT FK_5FA2AB5BFC8D4CAB FOREIGN KEY (topic_source) REFERENCES gs_api_topic (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_topic_requirements ADD CONSTRAINT FK_5FA2AB5BE5681C24 FOREIGN KEY (topic_target) REFERENCES gs_api_topic (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_topic_owner ADD CONSTRAINT FK_D994F4A71F55203D FOREIGN KEY (topic_id) REFERENCES gs_api_topic (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_topic_owner ADD CONSTRAINT FK_D994F4A7A76ED395 FOREIGN KEY (user_id) REFERENCES gs_api_user (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_topic_moderator ADD CONSTRAINT FK_C36A061F55203D FOREIGN KEY (topic_id) REFERENCES gs_api_topic (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_topic_moderator ADD CONSTRAINT FK_C36A06A76ED395 FOREIGN KEY (user_id) REFERENCES gs_api_user (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_venue ADD CONSTRAINT FK_5BCDF1ACF5B7AF75 FOREIGN KEY (address_id) REFERENCES gs_api_address (id)');
            $this->addSql('ALTER TABLE gs_api_year ADD CONSTRAINT FK_5DAA58C6E6389D24 FOREIGN KEY (society_id) REFERENCES gs_api_society (id)');
            $this->addSql('ALTER TABLE gs_api_year_user ADD CONSTRAINT FK_63A067CB40C1FEA7 FOREIGN KEY (year_id) REFERENCES gs_api_year (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE gs_api_year_user ADD CONSTRAINT FK_63A067CBA76ED395 FOREIGN KEY (user_id) REFERENCES gs_api_user (id) ON DELETE CASCADE');
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
            $this->addSql('ALTER TABLE test_api_lexik_email_translation DROP FOREIGN KEY FK_71045335A832C1C9');
            $this->addSql('ALTER TABLE test_api_activity_email DROP FOREIGN KEY FK_C8FD7E68131A730F');
            $this->addSql('ALTER TABLE test_api_society DROP FOREIGN KEY FK_BF24AE2BFAE5C615');
            $this->addSql('ALTER TABLE test_api_lexik_email DROP FOREIGN KEY FK_C33833E58C22AA1A');
            $this->addSql('ALTER TABLE test_api_lexik_layout_translation DROP FOREIGN KEY FK_272910888C22AA1A');
            $this->addSql('ALTER TABLE test_api_activity DROP FOREIGN KEY FK_53C923E583293531');
            $this->addSql('ALTER TABLE test_api_society DROP FOREIGN KEY FK_BF24AE2B9A009AF5');
            $this->addSql('ALTER TABLE test_api_payment DROP FOREIGN KEY FK_DF684BD49B6B5FBA');
            $this->addSql('ALTER TABLE test_api_registration DROP FOREIGN KEY FK_6E0A05259B6B5FBA');
            $this->addSql('ALTER TABLE test_api_activity_user DROP FOREIGN KEY FK_8DED4D7581C06096');
            $this->addSql('ALTER TABLE test_api_activity_email DROP FOREIGN KEY FK_C8FD7E6881C06096');
            $this->addSql('ALTER TABLE test_api_category DROP FOREIGN KEY FK_F9F1337E81C06096');
            $this->addSql('ALTER TABLE test_api_discount DROP FOREIGN KEY FK_1E5D9EB181C06096');
            $this->addSql('ALTER TABLE test_api_topic DROP FOREIGN KEY FK_3B481D0181C06096');
            $this->addSql('ALTER TABLE test_api_account DROP FOREIGN KEY FK_CF76997DF5B7AF75');
            $this->addSql('ALTER TABLE test_api_society DROP FOREIGN KEY FK_BF24AE2BF5B7AF75');
            $this->addSql('ALTER TABLE test_api_venue DROP FOREIGN KEY FK_3799D817F5B7AF75');
            $this->addSql('ALTER TABLE test_api_category_discount DROP FOREIGN KEY FK_C97C40B12469DE2');
            $this->addSql('ALTER TABLE test_api_topic DROP FOREIGN KEY FK_3B481D0112469DE2');
            $this->addSql('ALTER TABLE test_api_category_discount DROP FOREIGN KEY FK_C97C40B4C7C611F');
            $this->addSql('ALTER TABLE test_api_payment_item DROP FOREIGN KEY FK_323A10E84C7C611F');
            $this->addSql('ALTER TABLE test_api_invoice DROP FOREIGN KEY FK_2225D89D4C3A3BB');
            $this->addSql('ALTER TABLE test_api_payment_item DROP FOREIGN KEY FK_323A10E84C3A3BB');
            $this->addSql('ALTER TABLE test_api_payment_item DROP FOREIGN KEY FK_323A10E8833D8F43');
            $this->addSql('ALTER TABLE test_api_registration DROP FOREIGN KEY FK_6E0A05253237F1EE');
            $this->addSql('ALTER TABLE test_api_year DROP FOREIGN KEY FK_F160E92E6389D24');
            $this->addSql('ALTER TABLE test_api_activity DROP FOREIGN KEY FK_53C923E53B0ECC9');
            $this->addSql('ALTER TABLE test_api_registration DROP FOREIGN KEY FK_6E0A05251F55203D');
            $this->addSql('ALTER TABLE test_api_schedule DROP FOREIGN KEY FK_A5853B441F55203D');
            $this->addSql('ALTER TABLE test_api_topic_requirements DROP FOREIGN KEY FK_995C77F1FC8D4CAB');
            $this->addSql('ALTER TABLE test_api_topic_requirements DROP FOREIGN KEY FK_995C77F1E5681C24');
            $this->addSql('ALTER TABLE test_api_topic_owner DROP FOREIGN KEY FK_60EAF0D51F55203D');
            $this->addSql('ALTER TABLE test_api_topic_moderator DROP FOREIGN KEY FK_C5FDF59D1F55203D');
            $this->addSql('ALTER TABLE test_api_account DROP FOREIGN KEY FK_CF76997DA76ED395');
            $this->addSql('ALTER TABLE test_api_activity_user DROP FOREIGN KEY FK_8DED4D75A76ED395');
            $this->addSql('ALTER TABLE test_api_topic_owner DROP FOREIGN KEY FK_60EAF0D5A76ED395');
            $this->addSql('ALTER TABLE test_api_topic_moderator DROP FOREIGN KEY FK_C5FDF59DA76ED395');
            $this->addSql('ALTER TABLE test_api_year_user DROP FOREIGN KEY FK_49C661CA76ED395');
            $this->addSql('ALTER TABLE test_api_schedule DROP FOREIGN KEY FK_A5853B4440A73EBA');
            $this->addSql('ALTER TABLE test_api_activity DROP FOREIGN KEY FK_53C923E540C1FEA7');
            $this->addSql('ALTER TABLE test_api_year_user DROP FOREIGN KEY FK_49C661C40C1FEA7');
            $this->addSql('DROP TABLE test_api_lexik_email');
            $this->addSql('DROP TABLE test_api_lexik_email_translation');
            $this->addSql('DROP TABLE test_api_lexik_layout');
            $this->addSql('DROP TABLE test_api_lexik_layout_translation');
            $this->addSql('DROP TABLE test_api_account');
            $this->addSql('DROP TABLE test_api_activity');
            $this->addSql('DROP TABLE test_api_activity_user');
            $this->addSql('DROP TABLE test_api_activity_email');
            $this->addSql('DROP TABLE test_api_address');
            $this->addSql('DROP TABLE test_api_category');
            $this->addSql('DROP TABLE test_api_category_discount');
            $this->addSql('DROP TABLE test_api_discount');
            $this->addSql('DROP TABLE test_api_invoice');
            $this->addSql('DROP TABLE test_api_payment');
            $this->addSql('DROP TABLE test_api_payment_item');
            $this->addSql('DROP TABLE test_api_registration');
            $this->addSql('DROP TABLE test_api_schedule');
            $this->addSql('DROP TABLE test_api_society');
            $this->addSql('DROP TABLE test_api_topic');
            $this->addSql('DROP TABLE test_api_topic_requirements');
            $this->addSql('DROP TABLE test_api_topic_owner');
            $this->addSql('DROP TABLE test_api_topic_moderator');
            $this->addSql('DROP TABLE test_api_user');
            $this->addSql('DROP TABLE test_api_venue');
            $this->addSql('DROP TABLE test_api_year');
            $this->addSql('DROP TABLE test_api_year_user');

        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_lexik_email_translation DROP FOREIGN KEY FK_5EBFC31A832C1C9');
            $this->addSql('ALTER TABLE gs_api_activity_email DROP FOREIGN KEY FK_4C595BD0131A730F');
            $this->addSql('ALTER TABLE gs_api_society DROP FOREIGN KEY FK_FAA72D60FAE5C615');
            $this->addSql('ALTER TABLE gs_api_lexik_email DROP FOREIGN KEY FK_7A4637978C22AA1A');
            $this->addSql('ALTER TABLE gs_api_lexik_layout_translation DROP FOREIGN KEY FK_20303B3E8C22AA1A');
            $this->addSql('ALTER TABLE gs_api_activity DROP FOREIGN KEY FK_B282387E83293531');
            $this->addSql('ALTER TABLE gs_api_society DROP FOREIGN KEY FK_FAA72D609A009AF5');
            $this->addSql('ALTER TABLE gs_api_payment DROP FOREIGN KEY FK_9AEBC89F9B6B5FBA');
            $this->addSql('ALTER TABLE gs_api_registration DROP FOREIGN KEY FK_D0B86B319B6B5FBA');
            $this->addSql('ALTER TABLE gs_api_activity_user DROP FOREIGN KEY FK_97892B6681C06096');
            $this->addSql('ALTER TABLE gs_api_activity_email DROP FOREIGN KEY FK_4C595BD081C06096');
            $this->addSql('ALTER TABLE gs_api_category DROP FOREIGN KEY FK_18BA28E581C06096');
            $this->addSql('ALTER TABLE gs_api_discount DROP FOREIGN KEY FK_FF16852A81C06096');
            $this->addSql('ALTER TABLE gs_api_topic DROP FOREIGN KEY FK_571C34BA81C06096');
            $this->addSql('ALTER TABLE gs_api_account DROP FOREIGN KEY FK_8AF51A36F5B7AF75');
            $this->addSql('ALTER TABLE gs_api_society DROP FOREIGN KEY FK_FAA72D60F5B7AF75');
            $this->addSql('ALTER TABLE gs_api_venue DROP FOREIGN KEY FK_5BCDF1ACF5B7AF75');
            $this->addSql('ALTER TABLE gs_api_category_discount DROP FOREIGN KEY FK_FE92DC3112469DE2');
            $this->addSql('ALTER TABLE gs_api_topic DROP FOREIGN KEY FK_571C34BA12469DE2');
            $this->addSql('ALTER TABLE gs_api_category_discount DROP FOREIGN KEY FK_FE92DC314C7C611F');
            $this->addSql('ALTER TABLE gs_api_payment_item DROP FOREIGN KEY FK_8C887EFC4C7C611F');
            $this->addSql('ALTER TABLE gs_api_invoice DROP FOREIGN KEY FK_67A65BD64C3A3BB');
            $this->addSql('ALTER TABLE gs_api_payment_item DROP FOREIGN KEY FK_8C887EFC4C3A3BB');
            $this->addSql('ALTER TABLE gs_api_payment_item DROP FOREIGN KEY FK_8C887EFC833D8F43');
            $this->addSql('ALTER TABLE gs_api_registration DROP FOREIGN KEY FK_D0B86B313237F1EE');
            $this->addSql('ALTER TABLE gs_api_year DROP FOREIGN KEY FK_5DAA58C6E6389D24');
            $this->addSql('ALTER TABLE gs_api_activity DROP FOREIGN KEY FK_B282387E3B0ECC9');
            $this->addSql('ALTER TABLE gs_api_registration DROP FOREIGN KEY FK_D0B86B311F55203D');
            $this->addSql('ALTER TABLE gs_api_schedule DROP FOREIGN KEY FK_44CE20DF1F55203D');
            $this->addSql('ALTER TABLE gs_api_topic_requirements DROP FOREIGN KEY FK_5FA2AB5BFC8D4CAB');
            $this->addSql('ALTER TABLE gs_api_topic_requirements DROP FOREIGN KEY FK_5FA2AB5BE5681C24');
            $this->addSql('ALTER TABLE gs_api_topic_owner DROP FOREIGN KEY FK_D994F4A71F55203D');
            $this->addSql('ALTER TABLE gs_api_topic_moderator DROP FOREIGN KEY FK_C36A061F55203D');
            $this->addSql('ALTER TABLE gs_api_account DROP FOREIGN KEY FK_8AF51A36A76ED395');
            $this->addSql('ALTER TABLE gs_api_activity_user DROP FOREIGN KEY FK_97892B66A76ED395');
            $this->addSql('ALTER TABLE gs_api_topic_owner DROP FOREIGN KEY FK_D994F4A7A76ED395');
            $this->addSql('ALTER TABLE gs_api_topic_moderator DROP FOREIGN KEY FK_C36A06A76ED395');
            $this->addSql('ALTER TABLE gs_api_year_user DROP FOREIGN KEY FK_63A067CBA76ED395');
            $this->addSql('ALTER TABLE gs_api_schedule DROP FOREIGN KEY FK_44CE20DF40A73EBA');
            $this->addSql('ALTER TABLE gs_api_activity DROP FOREIGN KEY FK_B282387E40C1FEA7');
            $this->addSql('ALTER TABLE gs_api_year_user DROP FOREIGN KEY FK_63A067CB40C1FEA7');
            $this->addSql('DROP TABLE gs_api_lexik_email');
            $this->addSql('DROP TABLE gs_api_lexik_email_translation');
            $this->addSql('DROP TABLE gs_api_lexik_layout');
            $this->addSql('DROP TABLE gs_api_lexik_layout_translation');
            $this->addSql('DROP TABLE gs_api_account');
            $this->addSql('DROP TABLE gs_api_activity');
            $this->addSql('DROP TABLE gs_api_activity_user');
            $this->addSql('DROP TABLE gs_api_activity_email');
            $this->addSql('DROP TABLE gs_api_address');
            $this->addSql('DROP TABLE gs_api_category');
            $this->addSql('DROP TABLE gs_api_category_discount');
            $this->addSql('DROP TABLE gs_api_discount');
            $this->addSql('DROP TABLE gs_api_invoice');
            $this->addSql('DROP TABLE gs_api_payment');
            $this->addSql('DROP TABLE gs_api_payment_item');
            $this->addSql('DROP TABLE gs_api_registration');
            $this->addSql('DROP TABLE gs_api_schedule');
            $this->addSql('DROP TABLE gs_api_society');
            $this->addSql('DROP TABLE gs_api_topic');
            $this->addSql('DROP TABLE gs_api_topic_requirements');
            $this->addSql('DROP TABLE gs_api_topic_owner');
            $this->addSql('DROP TABLE gs_api_topic_moderator');
            $this->addSql('DROP TABLE gs_api_user');
            $this->addSql('DROP TABLE gs_api_venue');
            $this->addSql('DROP TABLE gs_api_year');
            $this->addSql('DROP TABLE gs_api_year_user');
        }
    }
}
