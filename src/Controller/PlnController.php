<?php

namespace App\Controller;

use App\Entity\AbstractWallet;
use App\Entity\Pln;
use App\Form\PlnType;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '/{_locale}/pln',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class PlnController extends AbstractAppPaginator
{
    public function __construct(
        private readonly BalanceUpdaterAccountInterface $walletUpdater,
        private readonly AccountRepositoryInterface     $plnRepository,
    ) {
    }

    #[Route('/', name: 'pln_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('pln/index.html.twig', [
            'pager' => $this->getPagerfanta(
                $request,
                $this->plnRepository->getAllRecordsQueryBuilder(),
            ),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'pln_new', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $pln = new Pln();
        $form = $this->createForm(PlnType::class, $pln);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plnRepository->save($pln, true);
            $this->walletUpdater->setPreviousId($this->plnRepository, $pln->getId());
            $this->walletUpdater->compute($this->plnRepository, $pln->getId());

            return $this->redirectToRoute('pln_index');
        }

        return $this->render('pln/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'pln_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pln $pln, string $route = ''): RedirectResponse|Response
    {
        $route = ('' === $route || '0' === $route) ? 'pln_index' : $route;
        $form = $this->createForm(PlnType::class, $pln);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->walletUpdater->setPreviousId($this->plnRepository, $pln->getId());
            $this->plnRepository->save($pln, true);
            $this->walletUpdater->compute($this->plnRepository, $pln->getId());

            return $this->redirectToRoute($route);
        }

        return $this->render('pln/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/is-consistent/{id}/{boolAsString}', name: 'pln_is_consistent', methods: ['POST'])]
    public function isConsistent(
        Request $request,
        Pln     $pln,
        string  $boolAsString = '',
        string  $route = '',
    ): RedirectResponse {
        $route = ('' === $route || '0' === $route) ? 'pln_index' : $route;
        if ($this->isCsrfTokenValid('is_consistent' . $pln->getId(), (string) $request->request->get('_token'))) {
            switch ($boolAsString) {
                case "true":
                    $pln->setIsConsistent(true);
                    break;
                case "false":
                    $pln->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('pln_index');
            }
            $this->plnRepository->save($pln, true);
        }

        return $this->redirectToRoute($route);
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'pln_delete', methods: ['POST'])]
    public function delete(Request $request, Pln $pln, string $route = ''): RedirectResponse
    {
        $route = ('' === $route || '0' === $route) ? 'pln_index' : $route;
        if ($this->isCsrfTokenValid('delete' . $pln->getId(), (string) $request->request->get('_token'))) {
            $pln->setAmount(0);
            $this->walletUpdater->setPreviousId($this->plnRepository, $pln->getId());
            $this->walletUpdater->compute($this->plnRepository, $pln->getId());
            $this->plnRepository->remove($pln, true);
        }

        return $this->redirectToRoute($route);
    }

    #[Route('/check', name: 'pln_check', methods: ['GET'])]
    public function check(
        BalanceSupervisorInterface $supervisor,
        TranslatorInterface $translator,
    ): RedirectResponse {
        /** @var AbstractWallet[] $plns */
        $plns = $this->plnRepository->getAllRecords();
        $supervisor->setWallets($plns);
        $generator = $supervisor->crawl($this->plnRepository);
        $caught = false;
        /** @var Pln $pln */
        foreach ($generator as $pln) {
            $this->addFlash('error', $pln->__toString());
            $caught = true;
        }
        if (false === $caught) {
            $this->addFlash('success', $translator->trans('Passed'));
        }

        return $this->redirectToRoute('pln_index');
    }
}
