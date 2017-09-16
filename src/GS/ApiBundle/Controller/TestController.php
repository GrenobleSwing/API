<?php

namespace GS\ApiBundle\Controller;

use GS\ETransactionBundle\Entity\Config;
use GS\ETransactionBundle\Entity\Environment;
use GS\ETransactionBundle\Entity\Payment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TestController extends Controller
{
    /**
     * @Route("/", name="test_paiement")
     */
    public function indexAction()
    {
        $env = new Environment();
        $env->setHmacKey("afba1833ae41a805c55d4e0665a9b5e9e592818729746a3657073fcf8c7cb29d626032829802fed1cdd8e198f29251cc01e7177801f36c33eac5c55a956eccf4");
        $env->setName("test");
        $env->setValidIps(array("195.101.99.76"));
        $env->setUrlClassique("https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi");
        $env->setUrlLight("https://preprod-tpeweb.e-transactions.fr/cgi/MYframepagepaiement_ip.cgi");
        $env->setUrlMobile("https://preprod-tpeweb.e-transactions.fr/cgi/ChoixPaiementMobile.cgi");

        $config = new Config();
        $config->setName("Grenoble Swing");
        $config->addEnvironment($env);
        $config->setIdentifiant("733522441");
        $config->setSite("1796291");
        $config->setRang("01");

        $payment = new Payment();
        $payment->setPorteur("bibi020886@gmail.com");
        $payment->setCmd("test" . uniqid());
        $payment->setTotal(12345);
        $payment->setEnvironment($env);
        $payment->setIpnUrl($this->generateUrl('gse_transaction_ipn', array(), UrlGeneratorInterface::ABSOLUTE_URL));
        $payment->setUrlAnnule($this->generateUrl('gs_api_return', array('status' => 'cancel'), UrlGeneratorInterface::ABSOLUTE_URL));
        $payment->setUrlEffectue($this->generateUrl('gs_api_return', array('status' => 'success'), UrlGeneratorInterface::ABSOLUTE_URL));
        $payment->setUrlRefuse($this->generateUrl('gs_api_return', array('status' => 'rejected'), UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('GSApiBundle:Default:test.html.twig', array(
            'payment' => $payment,
        ));
    }

    /**
     * @Route("/return/{status}", name="return")
     */
    public function returnAction($status, Request $request)
    {
        var_dump($request->query);
        $testSignature = $this->get('gs.e_transaction.signature.service')->verifySignature();
        return new Response($status . ' ' . $testSignature);
    }

}
