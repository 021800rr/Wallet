<?php

namespace App\Controller;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Entity\Pln;
use App\Form\TransferToBackupType;
use App\Form\TransferToPlnType;
use App\Repository\ContractorRepositoryInterface;
use App\Service\Transfer\TransferInterface;
use Exception;
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
    private Contractor $internalTransferOwner;

    /**
     * @throws Exception
     */
    public function __construct(ContractorRepositoryInterface $contractorRepository)
    {
        $this->internalTransferOwner = $contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
    }

    #[Route('/', name: 'transfer_index', methods: ['GET'])]
    public function index(): Response
    {
        $backup = new Backup();
        $backupForm = $this->createForm(TransferToBackupType::class, $backup, [
            'action' => $this->generateUrl('transfer_to_backup')
        ]);

        $pln = new Pln();
        $plnForm = $this->createForm(TransferToPlnType::class, $pln, [
            'action' => $this->generateUrl('transfer_to_pln')
        ]);

        return $this->render('transfer/index.html.twig', [
            'backup' => $backupForm->createView(),
            'pln' => $plnForm->createView(),
        ]);
    }

    #[Route('/transfer-to-backup', name: 'transfer_to_backup', methods: ['POST'])]
    public function transferToBackup(Request $request, TransferInterface $agent): RedirectResponse
    {
        $backup = new Backup();
        $backup->setContractor($this->internalTransferOwner);
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

    #[Route('/transfer-to-pln', name: 'transfer_to_pln', methods: ['POST'])]
    public function transferToPln(Request $request, TransferInterface $agent): RedirectResponse
    {
        $pln = new Pln();
        $pln->setContractor($this->internalTransferOwner);
        $plnForm = $this->createForm(TransferToPlnType::class, $pln);
        $plnForm->handleRequest($request);
        if ($plnForm->isSubmitted() && $plnForm->isValid()) {
            $agent->moveToPln($pln);

            return $this->redirectToRoute('pln_index');
        }

        return $this->redirectToRoute('transfer_index');
    }
}
