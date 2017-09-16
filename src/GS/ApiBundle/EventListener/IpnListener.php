<?php

namespace GS\ApiBundle\EventListener;

use Doctrine\ORM\EntityManager;
use GS\ApiBundle\Entity\Invoice;
use GS\ETransactionBundle\Event\IpnEvent;
use Symfony\Component\Filesystem\Filesystem;

class IpnListener
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param string     $rootDir
     * @param Filesystem $filesystem
     */
    public function __construct($rootDir, Filesystem $filesystem, EntityManager $entityManager)
    {
        $this->rootDir = $rootDir;
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
    }

    /**
     * @param IpnEvent $event
     */
    public function onIpnReceived(IpnEvent $event)
    {
        $path = sprintf('%s/../data/%s', $this->rootDir, date('Y\/m\/d\/'));
        $this->filesystem->mkdir($path);
        $content = sprintf('Signature verification : %s%s', $event->isVerified() ? 'OK' : 'KO', PHP_EOL);
        foreach ($event->getData() as $key => $value) {
            $content .= sprintf("%s:%s%s", $key, $value, PHP_EOL);
        }
        file_put_contents(
            sprintf('%s%s.txt', $path, time()),
            $content
        );

        if ( !$event->isVerified() ) {
            return;
        }
        $em = $this->entityManager;
        $data = $event->getData();

        $payment = $em
                ->getRepository('GSApiBundle:Payment')
                ->findOneByRef($data['Ref']);

        if ('00000' != $data['Erreur']) {
            $em->remove($payment);
        } else {
            $payment->getAccount()->addPayment($payment);
            $payment->setStatus('PAID');

            $repo = $em->getRepository('GSApiBundle:Invoice');
            if (null === $repo->findOneByPayment($payment)) {
                $prefix = $payment->getDate()->format('Y');
                $invoiceNumber = $repo->countByNumber($prefix) + 1;
                $invoice = new Invoice($payment);
                $invoice->setNumber($prefix . sprintf('%05d', $invoiceNumber));
                $invoice->setDate($payment->getDate());

                $this->get('gsapi.payment.service')->sendEmail($payment);

                $em->persist($invoice);
            }
        }
        $em->flush();

    }
}
