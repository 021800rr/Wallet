<?php

namespace App\Controller;

use App\Entity\Contractor;
use App\Form\ContractorType;
use App\Repository\ContractorRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.supported_locales%>}/contractor")
 * @IsGranted("ROLE_ADMIN")
 */
class ContractorController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="contractor_index", methods={"GET"})
     */
    public function index(ContractorRepository $contractorRepository, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $contractorRepository->getPaginator($offset);

        return $this->render('contractor/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - WalletRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + WalletRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    /**
     * @Route("/new", name="contractor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $contractor = new Contractor();
        $form = $this->createForm(ContractorType::class, $contractor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contractor);
            $this->entityManager->flush();

            return $this->redirectToRoute('contractor_index');
        }

        return $this->render('contractor/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="contractor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Contractor $contractor): Response
    {
        $form = $this->createForm(ContractorType::class, $contractor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contractor_index');
        }

        return $this->render('contractor/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="contractor_delete", methods={"POST"})
     */
    public function delete(Request $request, Contractor $contractor): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contractor->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($contractor);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('contractor_index');
    }
}
