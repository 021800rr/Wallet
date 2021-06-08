<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('wallet_index', ['_locale' => 'pl']);
    }
}
