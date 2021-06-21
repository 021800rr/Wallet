<?php

namespace App\Controller;

use App\Entity\Backup;
use App\Form\BackupType;
use App\Repository\BackupRepository;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
    private BalanceUpdaterInterface $updater;
    private BackupRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        BalanceUpdaterInterface $updater,
        BackupRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->updater = $updater;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="backup_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->repository->getPaginator($offset);

        return $this->render('backup/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - BackupRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + BackupRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="backup_edit", methods={"GET","POST"})
     * @throws Exception
     */
    public function edit(Request $request, Backup $backup): Response
    {
        $form = $this->createForm(BackupType::class, $backup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->updater->compute($this->repository);

            return $this->redirectToRoute('backup_index');
        }

        return $this->render('backup/form.html.twig', [
            'backup' => $backup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="backup_delete", methods={"POST"})
     * @throws Exception
     */
    public function delete(Request $request, Backup $backup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$backup->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($backup);
            $this->entityManager->flush();
            $this->updater->compute($this->repository);
        }

        return $this->redirectToRoute('backup_index');
    }
}
