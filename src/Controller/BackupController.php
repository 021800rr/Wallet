<?php

namespace App\Controller;

use App\Entity\Backup;
use App\Form\BackupType;
use App\Repository\BackupRepository;
use App\Repository\WalletRepository;
use App\Service\UpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.supported_locales%>}/backup")
 * @IsGranted("ROLE_ADMIN")
 */
class BackupController extends AbstractController
{
    private UpdaterInterface $updater;
    private BackupRepository $backupRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UpdaterInterface $backupUpdater,
        BackupRepository $backupRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->updater = $backupUpdater;
        $this->backupRepository = $backupRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="backup_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->backupRepository->getPaginator($offset);

        return $this->render('backup/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - BackupRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + BackupRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="backup_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Backup $backup): Response
    {
        $form = $this->createForm(BackupType::class, $backup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->updater->compute($this->backupRepository);

            return $this->redirectToRoute('backup_index');
        }

        return $this->render('backup/form.html.twig', [
            'backup' => $backup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="backup_delete", methods={"POST"})
     */
    public function delete(Request $request, Backup $backup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$backup->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($backup);
            $this->entityManager->flush();
            $this->updater->compute($this->backupRepository);
        }

        return $this->redirectToRoute('backup_index');
    }
}
