<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use GS\StructureBundle\Entity\Society;
use Lexik\Bundle\MailerBundle\Entity\Email;
use Lexik\Bundle\MailerBundle\Entity\EmailTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180826140353 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql('ALTER TABLE test_api_society ADD email_payment_failure_template_id INT DEFAULT NULL');
            $this->addSql('ALTER TABLE test_api_society ADD CONSTRAINT FK_BF24AE2BF2C0F037 FOREIGN KEY (email_payment_failure_template_id) REFERENCES test_api_lexik_email (id)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_BF24AE2BF2C0F037 ON test_api_society (email_payment_failure_template_id)');
            $this->addSql('ALTER TABLE test_api_payment ADD parent_id INT DEFAULT NULL, ADD already_paid DOUBLE PRECISION NOT NULL');
            $this->addSql('ALTER TABLE test_api_payment ADD CONSTRAINT FK_DF684BD4727ACA70 FOREIGN KEY (parent_id) REFERENCES test_api_payment (id)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_DF684BD4727ACA70 ON test_api_payment (parent_id)');
        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_payment ADD parent_id INT DEFAULT NULL, ADD already_paid DOUBLE PRECISION NOT NULL');
            $this->addSql('ALTER TABLE gs_api_payment ADD CONSTRAINT FK_9AEBC89F727ACA70 FOREIGN KEY (parent_id) REFERENCES gs_api_payment (id)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_9AEBC89F727ACA70 ON gs_api_payment (parent_id)');
            $this->addSql('ALTER TABLE gs_api_society ADD email_payment_failure_template_id INT DEFAULT NULL');
            $this->addSql('ALTER TABLE gs_api_society ADD CONSTRAINT FK_FAA72D60F2C0F037 FOREIGN KEY (email_payment_failure_template_id) REFERENCES gs_api_lexik_email (id)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_FAA72D60F2C0F037 ON gs_api_society (email_payment_failure_template_id)');
        }
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $qb = $em->getRepository('GSStructureBundle:Society')
                ->createQueryBuilder('s')
                ->setMaxResults( 1 )
                ->orderBy('s.id', 'ASC');
        $society = $qb->getQuery()->getSingleResult();

        $defaultBodyFailureFr = "";
        $defaultBodyFailureFr .= "Bonjour {{ payment.account.firstName }} {{ payment.account.lastName }},<br>\n";
        $defaultBodyFailureFr .= "Votre paiement en plusieurs fois de {{ payment.amount }}&euro;\n";
        $defaultBodyFailureFr .= "pour le règlement des inscriptions suivantes a échoué :\n";
        $defaultBodyFailureFr .= "{% import 'GSStructureBundle:PaymentItem:macros.html.twig' as macro %}\n";
        $defaultBodyFailureFr .= "<ul>\n";
        $defaultBodyFailureFr .= "    {% for item in payment.items %}\n";
        $defaultBodyFailureFr .= "        <li>\n";
        $defaultBodyFailureFr .= "            {{ macro.print(item) }}\n";
        $defaultBodyFailureFr .= "        </li>\n";
        $defaultBodyFailureFr .= "    {% endfor %}\n";
        $defaultBodyFailureFr .= "</ul>\n";
        $defaultBodyFailureFr .= "<br>";
        $defaultBodyFailureFr .= "Il reste {{ childPayment.amount }}&euro; à régler.";
        $defaultBodyFailureFr .= "<br>";
        $defaultBodyFailureFr .= "Merci d'effectuer ce paiment ici : {{ button|raw }}";
        $defaultBodyFailureFr .= "<br>";
        $defaultBodyFailureFr .= "Cordialement,\n";
        $defaultBodyFailureFr .= "<br>";
        $defaultBodyFailureFr .= "Grenoble Swing\n";
        $emailFailureTranslations = array(
            array(
                'locale' => 'fr',
                'subject' => '[Grenoble Swing] Echec paiement en plusieurs fois',
                'body' => $defaultBodyFailureFr,
                'from_address' => 'info@grenobleswing.com',
                'from_name' => 'Grenoble Swing',
            ),
            array(
                'locale' => 'en',
                'subject' => '[Grenoble Swing] Failure payment in multipe time',
                'body' => 'Put your text here.',
                'from_address' => 'info@grenobleswing.com',
                'from_name' => 'Grenoble Swing',
            ),
        );

        $layout = $society->getEmailPaymentLayout();

        $emailFailure = new Email();
        $emailFailure->setDescription('Template for payment failure emails');
        $emailFailure->setReference(uniqid('template_payment_failure_'));
        $emailFailure->setSpool(false);
        $emailFailure->setLayout($layout);
        $emailFailure->setUseFallbackLocale(true);
        foreach ($emailFailureTranslations as $trans) {
            $emailTranslation = new EmailTranslation();
            $emailTranslation->setLang($trans['locale']);
            $emailTranslation->setSubject($trans['subject']);
            $emailTranslation->setBody($trans['body']);
            $emailTranslation->setFromAddress($trans['from_address']);
            $emailTranslation->setFromName($trans['from_name']);
            $emailFailure->addTranslation($emailTranslation);
        }

        $society->setEmailPaymentFailureTemplate($emailFailure);

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        if ( $this->container->get('kernel')->getEnvironment() == "dev" ) {
            $this->addSql('ALTER TABLE test_api_payment DROP FOREIGN KEY FK_DF684BD4727ACA70');
            $this->addSql('DROP INDEX UNIQ_DF684BD4727ACA70 ON test_api_payment');
            $this->addSql('ALTER TABLE test_api_payment DROP parent_id, DROP already_paid');
            $this->addSql('ALTER TABLE test_api_society DROP FOREIGN KEY FK_BF24AE2BF2C0F037');
            $this->addSql('DROP INDEX UNIQ_BF24AE2BF2C0F037 ON test_api_society');
            $this->addSql('ALTER TABLE test_api_society DROP email_payment_failure_template_id');
        } elseif ( $this->container->get('kernel')->getEnvironment() == "prod" ) {
            $this->addSql('ALTER TABLE gs_api_payment DROP FOREIGN KEY FK_9AEBC89F727ACA70');
            $this->addSql('DROP INDEX UNIQ_9AEBC89F727ACA70 ON gs_api_payment');
            $this->addSql('ALTER TABLE gs_api_payment DROP parent_id, DROP already_paid');
            $this->addSql('ALTER TABLE gs_api_society DROP FOREIGN KEY FK_FAA72D60F2C0F037');
            $this->addSql('DROP INDEX UNIQ_FAA72D60F2C0F037 ON gs_api_society');
            $this->addSql('ALTER TABLE gs_api_society DROP email_payment_failure_template_id');

        }
    }
}
