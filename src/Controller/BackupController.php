<?php

namespace App\Controller;

use App\Entity\Backup;
use App\Form\BackupType;
use App\Form\InterestType;
use App\Repository\AccountRepositoryInterface;
use App\Repository\PaginatorEnum;
use App\Repository\BackupRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use App\Service\ExpectedBackup\CalculatorInterface;
use App\Service\Interest\InterestInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function __construct(
        private readonly BalanceUpdaterInterface   $backupUpdater,
        private readonly BackupRepositoryInterface $backupRepository,
        private readonly EntityManagerInterface    $entityManager
    ) {
    }

    #[Route('/', name: 'backup_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->backupRepository->getPaginator($offset);

        return $this->render('backup/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - PaginatorEnum::PerPage->value,
            'next' => min(count($paginator), $offset + PaginatorEnum::PerPage->value),
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
            $this->entityManager->persist($backup);
            $this->entityManager->flush();
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
        if ($this->isCsrfTokenValid('delete'.$backup->getId(), $request->request->get('_token'))) {
            $backup->setAmount(0);
            $this->backupUpdater->compute($this->backupRepository, $backup->getId());
            $this->entityManager->remove($backup);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('backup_index');
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    #[Route('/paymentsByMonth', name: 'backup_payments_by_month', methods: ['GET'])]
    public function paymentsByMonth(
        CalculatorInterface $calculator,
        WalletRepositoryInterface $walletRepository,
        AccountRepositoryInterface $chfRepository,
        AccountRepositoryInterface $eurRepository
    ): Response {
        /** @var array $backups */
        // [[yearMonth => 2021-06, sum_of_amount => 300],[yearMonth => 2021-05, sum_of_amount => 100]]
        $backups = $this->backupRepository->paymentsByMonth();
        $walletBalance = $walletRepository->getCurrentBalance();
        /** @var Backup[] $backupLastRecord */
        $backupLastRecord = $this->backupRepository->getLastRecord();

        return $this->render('backup/payments_by_month.html.twig', [
            'backups' => $backups,
            'expected' => $calculator->compute($backups),
            'walletBalance' => $walletBalance,
            'chfBalance' => $chfRepository->getCurrentBalance(),
            'eurBalance' => $eurRepository->getCurrentBalance(),
            'backupLastRecord' => $backupLastRecord,
            'total' => $walletBalance + $backupLastRecord->getBalance()
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
            $this->entityManager->persist($backup);
            $this->entityManager->flush();
            $this->backupUpdater->compute($this->backupRepository, $backup->getId());

            return $this->redirectToRoute('backup_index');
        }

        return $this->render('backup/interest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
