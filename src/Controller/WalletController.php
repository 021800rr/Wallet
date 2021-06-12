<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Form\WalletType;
use App\Repository\WalletRepository;
use App\Service\UpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
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
    private UpdaterInterface $updater;
    private WalletRepository $walletRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UpdaterInterface $updater,
        WalletRepository $walletRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->updater = $updater;
        $this->walletRepository = $walletRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="wallet_index")
     */
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->walletRepository->getPaginator($offset);

        return $this->render('wallet/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - WalletRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + WalletRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    /**
     * @Route("/new", name="wallet_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $wallet = new Wallet();
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($wallet);
            $this->entityManager->flush();
            $this->updater->compute($this->walletRepository);

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('wallet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="wallet_edit", methods={"GET", "POST"}))
     */
    public function edit(Request $request, Wallet $wallet): Response
    {
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->updater->compute($this->walletRepository);

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('wallet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/isconsistent/{id}/{bool}", name="wallet_is_consistent", methods={"POST"})
     */
    public function isConsistent(Request $request, Wallet $wallet, string $bool = ''): Response
    {
        if ($this->isCsrfTokenValid('is_consistent' . $wallet->getId(), $request->request->get('_token'))) {
            switch ($bool) {
                case "true":
                    $wallet->setIsConsistent(true);
                    break;
                case "false":
                    $wallet->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('wallet');
            }
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('wallet_index');
    }

    /**
     * @Route("/delete/{id}", name="wallet_delete", methods={"POST"})
     */
    public function delete(Request $request, Wallet $wallet): Response
    {
        if ($this->isCsrfTokenValid('delete' . $wallet->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($wallet);
            $this->entityManager->flush();
            $this->updater->compute($this->walletRepository);
        }

        return $this->redirectToRoute('wallet_index');
    }
}
