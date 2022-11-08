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
        private readonly BalanceUpdaterInterface $backupUpdater,
        private readonly BackupRepositoryInterface $repository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'backup_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->repository->getPaginator($offset);

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
            $this->backupUpdater->compute($this->repository, $backup->getId());

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
            $this->backupUpdater->compute($this->repository, $backup->getId());
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
        AccountRepositoryInterface $chf,
        AccountRepositoryInterface $eur
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
            'chfBalance' => $chf->getCurrentBalance(),
            'eurBalance' => $eur->getCurrentBalance(),
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
            $this->backupUpdater->compute($this->repository, $backup->getId());

            return $this->redirectToRoute('backup_index');
        }

        return $this->render('backup/interest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
