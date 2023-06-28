<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Form\WalletType;
use App\Repository\PaginatorEnum;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use App\Service\RequestParser\RequestParserInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '/{_locale}/wallet',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class WalletController extends AbstractController
{
    public function __construct(
        private readonly BalanceUpdaterFactoryInterface $walletFactory,
        private readonly WalletRepositoryInterface $walletRepository,
    ) {
    }

    #[Route('/', name: 'wallet_index', methods: ['GET'])]
    public function index(Request $request, RequestParserInterface $requestParser): Response
    {
        $offset = $requestParser->strategy(WalletController::class, $request);
        $paginator = $this->walletRepository->getPaginator($offset);

        return $this->render('wallet/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - PaginatorEnum::PerPage->value,
            'next' => min(count($paginator), $offset + PaginatorEnum::PerPage->value),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'wallet_new', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $wallet = new Wallet();
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->walletRepository->save($wallet, true);
            $this->walletFactory->create()->compute($this->walletRepository, $wallet->getId());

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('wallet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'wallet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Wallet $wallet, string $route = ''): RedirectResponse|Response
    {
        $route = (!empty($route)) ? $route : 'wallet_index';
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->walletRepository->save($wallet, true);
            $this->walletFactory->create()->compute($this->walletRepository, $wallet->getId());

            return $this->redirectToRoute($route);
        }

        return $this->render('wallet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/isconsistent/{id}/{boolAsString}', name: 'wallet_is_consistent', methods: ['POST'])]
    public function isConsistent(
        Request $request,
        Wallet $wallet,
        string $boolAsString = '',
        string $route = ''
    ): RedirectResponse {
        $route = (!empty($route)) ? $route : 'wallet_index';
        if ($this->isCsrfTokenValid('is_consistent' . $wallet->getId(), (string) $request->request->get('_token'))) {
            switch ($boolAsString) {
                case "true":
                    $wallet->setIsConsistent(true);
                    break;
                case "false":
                    $wallet->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('wallet_index');
            }
            $this->walletRepository->save($wallet, true);
        }

        return $this->redirectToRoute($route);
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'wallet_delete', methods: ['POST'])]
    public function delete(Request $request, Wallet $wallet, string $route = ''): RedirectResponse
    {
        $route = (!empty($route)) ? $route : 'wallet_index';
        if ($this->isCsrfTokenValid('delete' . $wallet->getId(), (string) $request->request->get('_token'))) {
            $wallet->setAmount(0);
            $this->walletFactory->create()->compute($this->walletRepository, $wallet->getId());
            $this->walletRepository->remove($wallet, true);
        }

        return $this->redirectToRoute($route);
    }

    #[Route('/check', name: 'wallet_check', methods: ['GET'])]
    public function check(
        BalanceSupervisorInterface $supervisor,
        TranslatorInterface $translator
    ): RedirectResponse {
        $supervisor->setWallets($this->walletRepository->getAllRecords());
        $generator = $supervisor->crawl($this->walletRepository);
        $caught = false;
        foreach ($generator as $wallet) {
            $this->addFlash('error', $wallet->__toString());
            $caught = true;
        }
        if (false === $caught) {
            $this->addFlash('success', $translator->trans('Passed'));
        }

        return $this->redirectToRoute('wallet_index');
    }
}
