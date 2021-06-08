<?php

namespace App\Controller;

use App\Entity\Backup;
use App\Entity\Wallet;
use App\Form\TransferToBackupType;
use App\Form\TransferToWalletType;
use App\Service\BackupBalanceUpdater;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.supported_locales%>}/transfer", requirements={"_locale": "%app.supported_locales%"})
 * @IsGranted("ROLE_ADMIN")
 */
class TransferController extends AbstractController
{
    /**
     * @Route("/", name="transfer_index", methods={"GET"})
     */
    public function index(Request $request): Response
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

    /**
     * @Route("/backup", name="transfer_to_backup", methods={"POST"})
     */
    public function backup(Request $request, BackupBalanceUpdater $transfer): Response
    {
        $backup = new Backup();
        $backupForm = $this->createForm(TransferToBackupType::class, $backup);
        $backupForm->handleRequest($request);
        if ($backupForm->isSubmitted() && $backupForm->isValid()) {
            $transfer->moveAssets($backup);

            return $this->redirectToRoute('backup_index');
        }

        return $this->redirectToRoute('transfer_index');
    }

    /**
     * @Route("/wallet", name="transfer_to_wallet", methods={"POST"})
     */
    public function wallet(Request $request, BackupBalanceUpdater $transfer): Response
    {
        $wallet = new Wallet();
        $walletForm = $this->createForm(TransferToWalletType::class, $wallet);
        $walletForm->handleRequest($request);
        if ($walletForm->isSubmitted() && $walletForm->isValid()) {
            $transfer->moveAssets($wallet);

            return $this->redirectToRoute('wallet_index');
        }

        return $this->redirectToRoute('transfer_index');
    }
}
