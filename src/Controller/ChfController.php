<?php

namespace App\Controller;

use App\Entity\Chf;
use App\Form\ChfType;
use App\Repository\AccountRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Repository\PaginatorEnum;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{_locale}/chf',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class ChfController extends AbstractController
{
    public function __construct(
        private readonly BalanceUpdaterInterface $walletUpdater,
        private readonly AccountRepositoryInterface $chf,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'chf_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->chf->getPaginator($offset);

        return $this->render('chf/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - PaginatorEnum::PerPage->value,
            'next' => min(count($paginator), $offset + PaginatorEnum::PerPage->value),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'chf_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContractorRepositoryInterface $contractorRepository): RedirectResponse|Response
    {
        $chf = new Chf();
        $form = $this->createForm(ChfType::class, $chf);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contractor = $contractorRepository->getInternalTransferOwner();
            $chf->setContractor($contractor);
            $this->entityManager->persist($chf);
            $this->entityManager->flush();
            $this->walletUpdater->compute($this->chf, $chf->getId());

            return $this->redirectToRoute('chf_index');
        }

        return $this->render('chf/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'chf_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chf $chf, string $route = ''): RedirectResponse|Response
    {
        $route = (!empty($route)) ? $route : 'chf_index';
        $form = $this->createForm(ChfType::class, $chf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($chf);
            $this->entityManager->flush();
            $this->walletUpdater->compute($this->chf, $chf->getId());

            return $this->redirectToRoute($route);
        }

        return $this->render('chf/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/isconsistent/{id}/{bool}', name: 'chf_is_consistent', methods: ['POST'])]
    public function isConsistent(Request $request, Chf $chf, string $bool = '', string $route = ''): RedirectResponse
    {
        $route = (!empty($route)) ? $route : 'chf_index';
        if ($this->isCsrfTokenValid('is_consistent' . $chf->getId(), $request->request->get('_token'))) {
            switch ($bool) {
                case "true":
                    $chf->setIsConsistent(true);
                    break;
                case "false":
                    $chf->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('chf_index');
            }
            $this->entityManager->persist($chf);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute($route);
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'chf_delete', methods: ['POST'])]
    public function delete(Request $request, Chf $chf, string $route = ''): RedirectResponse
    {
        $route = (!empty($route)) ? $route : 'chf_index';
        if ($this->isCsrfTokenValid('delete' . $chf->getId(), $request->request->get('_token'))) {
            $chf->setAmount(0);
            $this->walletUpdater->compute($this->chf, $chf->getId());
            $this->entityManager->remove($chf);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute($route);
    }
}
