<?php

namespace GS\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use GS\StructureBundle\Entity\Invoice;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("Invoice", pluralize=false)
 */
class InvoiceController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Invoice",
     *   description="Returns an existing Invoice",
     *   requirements={
     *     {
     *       "name"="invoice",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Invoice id"
     *     }
     *   },
     *   output="GS\StructureBundle\Entity\Invoice",
     *   statusCodes={
     *     200="Returns the Invoice in PDF",
     *   }
     * )
     * @Security("is_granted('view', invoice)")
     */
    public function getAction(Invoice $invoice)
    {
        $societies = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Society')
            ->findAll()
            ;
        $html = $this->renderView('GSApiBundle:Invoice:invoice.html.twig', array(
            'invoice' => $invoice,
            'society' => $societies[0],
        ));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
            )
        );
    }

    /**
     * @ApiDoc(
     *   section="Invoice",
     *   description="Returns a collection of Invoices",
     *   output="array<GS\StructureBundle\Entity\Invoice>",
     *   statusCodes={
     *     200="Returns all the Invoices",
     *   }
     * )
     * @Security("has_role('ROLE_TREASURER')")
     */
    public function cgetAction()
    {
        $listInvoices = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Invoice')
            ->findAll()
            ;

        $view = $this->view($listInvoices, 200);
        return $this->handleView($view);
    }

}
