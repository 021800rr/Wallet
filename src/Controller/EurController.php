<?php

namespace App\Controller;

use App\Entity\Eur;
use App\Form\EurType;
use App\Repository\AccountRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Repository\PaginatorEnum;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function __construct(
        private readonly BalanceUpdaterFactoryInterface $walletFactory,
        private readonly AccountRepositoryInterface $eurRepository,
    ) {
    }

    #[Route('/', name: 'eur_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->eurRepository->getPaginator($offset);

        return $this->render('eur/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - PaginatorEnum::PerPage->value,
            'next' => min(count($paginator), $offset + PaginatorEnum::PerPage->value),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'eur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContractorRepositoryInterface $contractorRepository): RedirectResponse|Response
    {
        $eur = new Eur();
        $contractor = $contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
        $eur->setContractor($contractor);

        return $this->upsert($eur, $request);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'eur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Eur $eur): RedirectResponse|Response
    {
        return $this->upsert($eur, $request);
    }

    #[Route('/isconsistent/{id}/{boolAsString}', name: 'eur_is_consistent', methods: ['POST'])]
    public function isConsistent(Request $request, Eur $eur, string $boolAsString = ''): RedirectResponse
    {
        if ($this->isCsrfTokenValid('is_consistent' . $eur->getId(), (string) $request->request->get('_token'))) {
            switch ($boolAsString) {
                case "true":
                    $eur->setIsConsistent(true);
                    break;
                case "false":
                    $eur->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('eur_index');
            }
            $this->eurRepository->save($eur, true);
        }

        return $this->redirectToRoute('eur_index');
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'eur_delete', methods: ['POST'])]
    public function delete(Request $request, Eur $eur): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $eur->getId(), (string) $request->request->get('_token'))) {
            $eur->setAmount(0);
            $this->walletFactory->create()->compute($this->eurRepository, $eur->getId());
            $this->eurRepository->remove($eur, true);
        }

        return $this->redirectToRoute('eur_index');
    }

    /**
     * @param Eur $eur
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    private function upsert(Eur $eur, Request $request): Response|RedirectResponse
    {
        $form = $this->createForm(EurType::class, $eur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->eurRepository->save($eur, true);
            $this->walletFactory->create()->compute($this->eurRepository, $eur->getId());

            return $this->redirectToRoute('eur_index');
        }

        return $this->render('eur/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
