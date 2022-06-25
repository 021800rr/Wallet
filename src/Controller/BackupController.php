<?php

namespace App\Controller;

use App\Entity\Backup;
use App\Form\BackupType;
use App\Form\InterestType;
use App\Repository\AppPaginatorInterface;
use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\EurRepository;
use App\Repository\WalletRepository;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use App\Service\ExpectedBackup\Calculator;
use App\Service\Interest;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{_locale}/backup',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class BackupController extends AbstractController
{
    private BalanceUpdaterInterface $updater;
    private BackupRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        BalanceUpdaterInterface $backupUpdater,
        BackupRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->updater = $backupUpdater;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'backup_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->repository->getPaginator($offset);

        return $this->render('backup/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - AppPaginatorInterface::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + AppPaginatorInterface::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/edit/{id}', name: 'backup_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Backup $backup): Response
    {
        $form = $this->createForm(BackupType::class, $backup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($backup);
            $this->entityManager->flush();
            $this->updater->compute($this->repository, $backup->getId());

            return $this->redirectToRoute('backup_index');
        }

        return $this->render('backup/form.html.twig', [
            'backup' => $backup,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'backup_delete', methods: ['POST'])]
    public function delete(Request $request, Backup $backup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$backup->getId(), $request->request->get('_token'))) {
            $backup->setAmount(0);
            $this->updater->compute($this->repository, $backup->getId());
            $this->entityManager->remove($backup);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('backup_index');
    }

    #[Route('/paymentsByMonth', name: 'backup_payments_by_month', methods: ['GET'])]
    public function paymentsByMonth(
        Calculator $calculator,
        WalletRepository $walletRepository,
        ChfRepository $chfRepository,
        EurRepository $eurRepository
    ): Response {
        $paginator = $this->repository->paymentsByMonth();
        $expected = $calculator->compute($paginator);
        $walletBalance = $walletRepository->getCurrentBalance();
        /** @var Backup[] $backupLastRecords */
        $backupLastRecord = $this->repository->getLastRecord();

        return $this->render('backup/payments_by_month.html.twig', [
            'paginator' => $paginator,
            'expected' => $expected,
            'walletBalance' => $walletBalance,
            'chfBalance' => $chfRepository->getCurrentBalance(),
            'eurBalance' => $eurRepository->getCurrentBalance(),
            'backupLastRecord' => $backupLastRecord,
            'total' => $walletBalance + $backupLastRecord->getBalance()
        ]);
    }

    #[Route('/interest', name: 'backup_interest', methods: ['GET', 'POST'])]
    public function newInterest(Request $request, Interest $interest)
    {
        $form = $this->createForm(InterestType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $backup = $interest->form2Backup($form);
            $this->entityManager->persist($backup);
            $this->entityManager->flush();
            $this->updater->compute($this->repository, $backup->getId());

            return $this->redirectToRoute('backup_index');
        }

        return $this->render('backup/interest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
