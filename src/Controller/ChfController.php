<?php

namespace App\Controller;

use App\Entity\Chf;
use App\Form\ChfType;
use App\Repository\ChfRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Repository\PaginatorEnum;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '/{_locale}/chf',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class ChfController extends AbstractController
{
    public function __construct(
        private readonly BalanceUpdaterInterface $walletUpdater,
        private readonly ChfRepositoryInterface $chfRepository,
    ) {
    }

    #[Route('/', name: 'chf_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->chfRepository->getPaginator($offset);

        return $this->render('chf/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - PaginatorEnum::PerPage->value,
            'next' => min(count($paginator), $offset + PaginatorEnum::PerPage->value),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'chf_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContractorRepositoryInterface $contractorRepository): RedirectResponse|Response
    {
        $chf = new Chf();
        $form = $this->createForm(ChfType::class, $chf);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contractor = $contractorRepository->getInternalTransferOwner();
            $chf->setContractor($contractor);
            $this->chfRepository->save($chf, true);
            $this->walletUpdater->compute($this->chfRepository, $chf->getId());

            return $this->redirectToRoute('chf_index');
        }

        return $this->render('chf/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'chf_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chf $chf): RedirectResponse|Response
    {
        $form = $this->createForm(ChfType::class, $chf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->chfRepository->save($chf, true);
            $this->walletUpdater->compute($this->chfRepository, $chf->getId());

            return $this->redirectToRoute('chf_index');
        }

        return $this->render('chf/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/isconsistent/{id}/{boolAsString}', name: 'chf_is_consistent', methods: ['POST'])]
    public function isConsistent(Request $request, Chf $chf, string $boolAsString = ''): RedirectResponse
    {
        if ($this->isCsrfTokenValid('is_consistent' . $chf->getId(), $request->request->get('_token'))) {
            switch ($boolAsString) {
                case "true":
                    $chf->setIsConsistent(true);
                    break;
                case "false":
                    $chf->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('chf_index');
            }
            $this->chfRepository->save($chf, true);
        }

        return $this->redirectToRoute('chf_index');
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'chf_delete', methods: ['POST'])]
    public function delete(Request $request, Chf $chf): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $chf->getId(), $request->request->get('_token'))) {
            $chf->setAmount(0);
            $this->walletUpdater->compute($this->chfRepository, $chf->getId());
            $this->chfRepository->remove($chf, true);
        }

        return $this->redirectToRoute('chf_index');
    }

    #[Route('/check', name: 'chf_check', methods: ['GET'])]
    public function check(
        BalanceSupervisorInterface $supervisor,
        SessionInterface $session,
        TranslatorInterface $translator
    ): RedirectResponse {
        $supervisor->setWallets($this->chfRepository->getAllRecords());
        $generator = $supervisor->crawl($this->chfRepository);
        $caught = false;
        foreach ($generator as $wallet) {
            $session->getFlashBag()->add('error', $wallet->__toString());
            $caught = true;
        }
        if (false === $caught) {
            $session->getFlashBag()->add('success', $translator->trans('Passed'));
        }

        return $this->redirectToRoute('chf_index');
    }
}
