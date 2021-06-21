<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Form\WalletType;
use App\Repository\WalletRepository;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use App\Service\RequestParser\RequestInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.supported_locales%>}/wallet")
 * @IsGranted("ROLE_ADMIN")
 */
class WalletController extends AbstractController
{
    private BalanceUpdaterInterface $updater;
    private WalletRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        BalanceUpdaterInterface $updater,
        WalletRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->updater = $updater;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="wallet_index")
     */
    public function index(
        Request $request,
        RequestInterface $parser
    ): Response {
        $offset = $parser->strategy(WalletController::class, $request);

        $paginator = $this->repository->getPaginator($offset);

        return $this->render('wallet/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - WalletRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + WalletRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    /**
     * @Route("/new", name="wallet_new", methods={"GET","POST"})
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $wallet = new Wallet();
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($wallet);
            $this->entityManager->flush();
            $this->updater->compute($this->repository);

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('wallet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="wallet_edit", methods={"GET", "POST"}))
     * @throws Exception
     */
    public function edit(Request $request, Wallet $wallet, string $route = ''): Response
    {
        $route = (!empty($route)) ? $route : 'wallet_index';
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->updater->compute($this->repository);

            return $this->redirectToRoute($route);
        }

        return $this->render('wallet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/isconsistent/{id}/{bool}", name="wallet_is_consistent", methods={"POST"})
     */
    public function isConsistent(Request $request, Wallet $wallet, string $bool = '', string $route = ''): Response
    {
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
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/delete/{id}", name="wallet_delete", methods={"POST"})
     * @throws Exception
     */
    public function delete(Request $request, Wallet $wallet, string $route = ''): Response
    {
        $route = (!empty($route)) ? $route : 'wallet_index';
        if ($this->isCsrfTokenValid('delete' . $wallet->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($wallet);
            $this->entityManager->flush();
            $this->updater->compute($this->repository);
        }

        return $this->redirectToRoute($route);
    }
}
