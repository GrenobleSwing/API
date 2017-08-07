<?php

namespace GS\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAnonymous()
    {
        $listTopics = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Topic')
            ->getOpenTopics()
            ;

        return $this->render('GSApiBundle:Default:index.html.twig', array(
                    'listTopics' => $listTopics
        ));
    }

}
