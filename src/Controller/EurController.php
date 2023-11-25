<?php

namespace App\Controller;

use App\Entity\AbstractWallet;
use App\Entity\Eur;
use App\Form\EurType;
use App\Repository\AccountRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '/{_locale}/eur',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class EurController extends AbstractAppPaginator
{
    public function __construct(
        private readonly BalanceUpdaterAccountInterface $walletUpdater,
        private readonly AccountRepositoryInterface     $eurRepository,
    ) {
    }

    #[Route('/', name: 'eur_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('eur/index.html.twig', [
            'pager' => $this->getPagerfanta(
                $request,
                $this->eurRepository->getAllRecordsQueryBuilder(),
            ),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'eur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContractorRepositoryInterface $contractorRepository): RedirectResponse|Response
    {
        $eur = new Eur();
        $internalTransferOwner = $contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
        $eur->setContractor($internalTransferOwner);

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

    #[Route('/is-consistent/{id}/{boolAsString}', name: 'eur_is_consistent', methods: ['POST'])]
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
            $this->walletUpdater->setPreviousId($this->eurRepository, $eur->getId());
            $this->walletUpdater->compute($this->eurRepository, $eur->getId());
            $this->eurRepository->remove($eur, true);
        }

        return $this->redirectToRoute('eur_index');
    }

    #[Route('/check', name: 'eur_check', methods: ['GET'])]
    public function check(
        BalanceSupervisorInterface $supervisor,
        TranslatorInterface $translator,
    ): RedirectResponse {
        /** @var AbstractWallet[] $eurs */
        $eurs = $this->eurRepository->getAllRecords();
        $supervisor->setWallets($eurs);
        $generator = $supervisor->crawl($this->eurRepository);
        $caught = false;
        /** @var Eur $eur */
        foreach ($generator as $eur) {
            $this->addFlash('error', $eur->__toString());
            $caught = true;
        }
        if (false === $caught) {
            $this->addFlash('success', $translator->trans('Passed'));
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
            if ($eur->getId()) {
                $this->walletUpdater->setPreviousId($this->eurRepository, $eur->getId());
                $this->eurRepository->save($eur, true);
            } else {
                $this->eurRepository->save($eur, true);
                $this->walletUpdater->setPreviousId($this->eurRepository, $eur->getId());
            }
            $this->walletUpdater->compute($this->eurRepository, $eur->getId());

            return $this->redirectToRoute('eur_index');
        }

        return $this->render('eur/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
