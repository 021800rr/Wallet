<?php

namespace App\Controller;

use App\Entity\Fee;
use App\Form\FeeType;
use App\Handler\FeeHandler;
use App\Repository\FeeRepositoryInterface;
use App\Service\FixedFees\FixedFeesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{_locale}/fee',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class FeeController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'fee_index', methods: ['GET'])]
    public function index(FeeRepositoryInterface $feeRepository): Response
    {
        return $this->render('fee/index.html.twig', [
            'fees' => $feeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'fee_new', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $fee = new Fee();
        $form = $this->createForm(FeeType::class, $fee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($fee);
            $this->entityManager->flush();

            return $this->redirectToRoute('fee_index');
        }

        return $this->render('fee/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'fee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fee $fee, EntityManagerInterface $entityManager): RedirectResponse|Response
    {
        $form = $this->createForm(FeeType::class, $fee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('fee_index');
        }

        return $this->render('fee/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'fee_delete', methods: ['POST'])]
    public function delete(Request $request, Fee $fee): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $fee->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($fee);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('fee_index');
    }

    #[Route('/insert', name: 'fee_insert_to_wallet', methods: ['POST'])]
    public function insert(
        FeeRepositoryInterface $feeRepository,
        Request                $request,
        FixedFeesInterface     $fixedFees,
        FeeHandler             $feeHandler,
    ): RedirectResponse|Response {
        if ($this->isCsrfTokenValid('fixedfees', $request->request->get('_token'))) {
            $feeHandler->handle($fixedFees);

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('fee/index.html.twig', [
            'fees' => $feeRepository->findAll(),
        ]);
    }
}
