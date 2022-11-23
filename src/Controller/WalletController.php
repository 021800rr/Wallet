<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Form\WalletType;
use App\Repository\PaginatorEnum;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use App\Service\RequestParser\RequestInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
        private readonly BalanceUpdaterInterface $walletUpdater,
        private readonly WalletRepositoryInterface $repository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'wallet_index', methods: ['GET'])]
    public function index(Request $request, RequestInterface $parser): Response
    {
        $offset = $parser->strategy(WalletController::class, $request);

        $paginator = $this->repository->getPaginator($offset);

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
            $this->entityManager->persist($wallet);
            $this->entityManager->flush();
            $this->walletUpdater->compute($this->repository, $wallet->getId());

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
            $this->entityManager->persist($wallet);
            $this->entityManager->flush();
            $this->walletUpdater->compute($this->repository, $wallet->getId());

            return $this->redirectToRoute($route);
        }

        return $this->render('wallet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/isconsistent/{id}/{bool}', name: 'wallet_is_consistent', methods: ['POST'])]
    public function isConsistent(
        Request $request,
        Wallet $wallet,
        string $bool = '',
        string $route = ''
    ): RedirectResponse {
        $route = (!empty($route)) ? $route : 'wallet_index';
        if ($this->isCsrfTokenValid('is_consistent' . $wallet->getId(), $request->request->get('_token'))) {
            switch ($bool) {
                case "true":
                    $wallet->setIsConsistent(true);
                    break;
                case "false":
                    $wallet->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('wallet_index');
            }
            $this->entityManager->persist($wallet);
            $this->entityManager->flush();
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
        if ($this->isCsrfTokenValid('delete' . $wallet->getId(), $request->request->get('_token'))) {
            $wallet->setAmount(0);
            $this->walletUpdater->compute($this->repository, $wallet->getId());
            $this->entityManager->remove($wallet);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute($route);
    }

    #[Route('/check', name: 'wallet_check', methods: ['GET'])]
    public function check(
        BalanceSupervisorInterface $supervisor,
        SessionInterface $session,
        TranslatorInterface $translator
    ): RedirectResponse {
        $supervisor->setWallets($this->repository->getAllRecords());
        /** @var Wallet[] $wallets */
        $wallets = $supervisor->crawl();
        $caught = false;
        foreach ($wallets as $wallet) {
            $session->getFlashBag()->add('error', $wallet->__toString());
            $caught = true;
        }
        if (false === $caught) {
            $session->getFlashBag()->add('success', $translator->trans('Passed'));
        }

        return $this->redirectToRoute('wallet_index');
    }
}
