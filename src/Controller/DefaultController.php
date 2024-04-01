<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    #[Route('/')]
    #[IsGranted('ROLE_ADMIN')]
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('pln_index', ['_locale' => 'pl']);
    }
}
