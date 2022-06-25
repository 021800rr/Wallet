<?php

namespace App\Controller;

use App\Entity\Eur;
use App\Form\EurType;
use App\Repository\AppPaginatorInterface;
use App\Repository\EurRepository;
use App\Repository\ContractorRepository;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{_locale}/eur',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class EurController extends AbstractController
{
    private BalanceUpdaterInterface $updater;
    private EurRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        BalanceUpdaterInterface $walletUpdater,
        EurRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->updater = $walletUpdater;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'eur_index', methods: ['GET'])]
    public function index(Request $request): Response {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->repository->getPaginator($offset);

        return $this->render('eur/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - AppPaginatorInterface::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + AppPaginatorInterface::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'eur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContractorRepository $contractorRepository): Response
    {
        $eur = new Eur();
        $form = $this->createForm(EurType::class, $eur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contractor = $contractorRepository->getInternalTransferOwner();
            $eur->setContractor($contractor);
            $this->entityManager->persist($eur);
            $this->entityManager->flush();
            $this->updater->compute($this->repository, $eur->getId());

            return $this->redirectToRoute('eur_index');
        }

        return $this->render('eur/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'eur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Eur $eur, string $route = ''): Response
    {
        $route = (!empty($route)) ? $route : 'eur_index';
        $form = $this->createForm(EurType::class, $eur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($eur);
            $this->entityManager->flush();
            $this->updater->compute($this->repository, $eur->getId());

            return $this->redirectToRoute($route);
        }

        return $this->render('eur/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/isconsistent/{id}/{bool}', name: 'eur_is_consistent', methods: ['POST'])]
    public function isConsistent(Request $request, Eur $eur, string $bool = '', string $route = ''): Response
    {
        $route = (!empty($route)) ? $route : 'eur_index';
        if ($this->isCsrfTokenValid('is_consistent' . $eur->getId(), $request->request->get('_token'))) {
            switch ($bool) {
                case "true":
                    $eur->setIsConsistent(true);
                    break;
                case "false":
                    $eur->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('eur_index');
            }
            $this->entityManager->persist($eur);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute($route);
    }

    #[Route('/delete/{id}', name: 'eur_delete', methods: ['POST'])]
    public function delete(Request $request, Eur $eur, string $route = ''): Response
    {
        $route = (!empty($route)) ? $route : 'eur_index';
        if ($this->isCsrfTokenValid('delete' . $eur->getId(), $request->request->get('_token'))) {
            $eur->setAmount(0);
            $this->updater->compute($this->repository, $eur->getId());
            $this->entityManager->remove($eur);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute($route);
    }
}
