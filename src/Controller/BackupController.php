<?php

namespace App\Controller;

use App\Entity\Backup;
use App\Form\BackupType;
use App\Form\InterestType;
use App\Repository\AccountRepositoryInterface;
use App\Repository\BackupRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use App\Service\ExpectedBackup\CalculatorInterface;
use App\Service\Interest\InterestInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    path: '/{_locale}/backup',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class BackupController extends AbstractAppPaginator
{
    public function __construct(
        private readonly BalanceUpdaterAccountInterface $backupUpdater,
        private readonly BackupRepositoryInterface      $backupRepository,
    ) {
    }

    #[Route('/', name: 'backup_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('backup/index.html.twig', [
            'pager' => $this->getPagerfanta(
                $request,
                $this->backupRepository->getAllRecordsQueryBuilder(),
            ),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'backup_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Backup $backup): RedirectResponse|Response
    {
        $form = $this->createForm(BackupType::class, $backup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->backupUpdater->setPreviousId($this->backupRepository, $backup->getId());
            $this->backupRepository->save($backup, true);
            $this->backupUpdater->compute($this->backupRepository, $backup->getId());

            return $this->redirectToRoute('backup_index');
        }

        return $this->render('backup/form.html.twig', [
            'backup' => $backup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'backup_delete', methods: ['POST'])]
    public function delete(Request $request, Backup $backup): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $backup->getId(), (string) $request->request->get('_token'))) {
            $backup->setAmount(0);
            $this->backupUpdater->setPreviousId($this->backupRepository, $backup->getId());
            $this->backupUpdater->compute($this->backupRepository, $backup->getId());
            $this->backupRepository->remove($backup, true);
        }

        return $this->redirectToRoute('backup_index');
    }

    #[Route('/payments-by-month', name: 'backup_payments_by_month', methods: ['GET'])]
    public function paymentsByMonth(
        CalculatorInterface        $calculator,
        AccountRepositoryInterface $plnRepository,
        AccountRepositoryInterface $chfRepository,
        AccountRepositoryInterface $eurRepository,
    ): Response {
        // $backups:
        // [
        //     [yearMonth => 2021-06, sum_of_amounts => 300],
        //     [yearMonth => 2021-05, sum_of_amounts => 100]
        // ]
        $backups = $this->backupRepository->paymentsByMonth();
        $plnBalance = $plnRepository->getCurrentBalance();
        /** @var Backup $backupLastRecord */
        $backupLastRecord = $this->backupRepository->getLastRecord();

        return $this->render('backup/payments_by_month.html.twig', [
            'backups' => $backups,
            'expected' => $calculator->compute($backups),
            'plnBalance' => $plnBalance,
            'chfBalance' => $chfRepository->getCurrentBalance(),
            'eurBalance' => $eurRepository->getCurrentBalance(),
            'backupLastRecord' => $backupLastRecord,
            'total' => $plnBalance + $backupLastRecord->getBalance()
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/interest', name: 'backup_interest', methods: ['GET', 'POST'])]
    public function newInterest(Request $request, InterestInterface $interest): RedirectResponse|Response
    {
        $form = $this->createForm(InterestType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $backup = $interest->form2Backup($form);
            $this->backupRepository->save($backup, true);
            $this->backupUpdater->setPreviousId($this->backupRepository, $backup->getId());
            $this->backupUpdater->compute($this->backupRepository, $backup->getId());

            return $this->redirectToRoute('backup_index');
        }

        return $this->render('backup/interest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
