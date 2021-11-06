<?php

namespace App\Controller;

use App\Entity\Fee;
use App\Form\FeeType;
use App\Repository\FeeRepository;
use App\Repository\WalletRepository;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use App\Service\FixedFees\FixedFeesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.supported_locales%>}/fee")
 * @IsGranted("ROLE_ADMIN")
 */
class FeeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="fee_index", methods={"GET"})
     */
    public function index(FeeRepository $feeRepository): Response
    {
        return $this->render('fee/index.html.twig', [
            'fees' => $feeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="fee_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
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

    /**
     * @Route("/edit/{id}", name="fee_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Fee $fee): Response
    {
        $form = $this->createForm(FeeType::class, $fee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fee_index');
        }

        return $this->render('fee/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="fee_delete", methods={"POST"})
     */
    public function delete(Request $request, Fee $fee): Response
    {
        if ($this->isCsrfTokenValid('delete' . $fee->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($fee);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('fee_index');
    }

    /**
     * @Route("/insert", name="fee_insert_to_wallet", methods={"POST"})
     * @throws Exception
     */
    public function insert(
        FeeRepository $feeRepository,
        Request $request,
        FixedFeesInterface $fixedFees
    ): Response {
        if ($this->isCsrfTokenValid('fixedfees', $request->request->get('_token'))) {
            $fixedFees->insert();

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('fee/index.html.twig', [
            'fees' => $feeRepository->findAll(),
        ]);
    }
}
