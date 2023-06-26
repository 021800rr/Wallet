<?php

namespace App\Controller;

use App\Entity\Backup;
use App\Entity\Wallet;
use App\Form\TransferToBackupType;
use App\Form\TransferToWalletType;
use App\Repository\ContractorRepository;
use App\Service\Transfer\TransferInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{_locale}/transfer',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class TransferController extends AbstractController
{
    public function __construct(private readonly ContractorRepository $contractorRepository)
    {
    }

    #[Route('/', name: 'transfer_index', methods: ['GET'])]
    public function index(): Response
    {
        $backup = new Backup();
        $backupForm = $this->createForm(TransferToBackupType::class, $backup, [
            'action' => $this->generateUrl('transfer_to_backup')
        ]);

        $wallet = new Wallet();
        $walletForm = $this->createForm(TransferToWalletType::class, $wallet, [
            'action' => $this->generateUrl('transfer_to_wallet')
        ]);

        return $this->render('transfer/index.html.twig', [
            'backup' => $backupForm->createView(),
            'wallet' => $walletForm->createView(),
        ]);
    }

    #[Route('/backup', name: 'transfer_to_backup', methods: ['POST'])]
    public function backup(Request $request, TransferInterface $agent): RedirectResponse
    {
        $backup = new Backup();
        $backup->setContractor($this->contractorRepository->getInternalTransferOwner());
        $backupForm = $this->createForm(TransferToBackupType::class, $backup);
        $backupForm->handleRequest($request);
        if ($backupForm->isSubmitted() && $backupForm->isValid()) {
            $post = $request->request->all();
            $currency = 0;
            if (isset($post['transfer_to_backup']['currency'])) {
                $currency = $post['transfer_to_backup']['currency'];
            }
            $agent->moveToBackup($backup, $currency);

            return $this->redirectToRoute('backup_index');
        }

        return $this->redirectToRoute('transfer_index');
    }

    #[Route('/wallet', name: 'transfer_to_wallet', methods: ['POST'])]
    public function wallet(Request $request, TransferInterface $agent): RedirectResponse
    {
        $wallet = new Wallet();
        $wallet->setContractor($this->contractorRepository->getInternalTransferOwner());
        $walletForm = $this->createForm(TransferToWalletType::class, $wallet);
        $walletForm->handleRequest($request);
        if ($walletForm->isSubmitted() && $walletForm->isValid()) {
            $agent->moveToWallet($wallet);

            return $this->redirectToRoute('wallet_index');
        }

        return $this->redirectToRoute('transfer_index');
    }
}
