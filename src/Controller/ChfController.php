<?php

namespace App\Controller;

use App\Entity\AbstractWallet;
use App\Entity\Chf;
use App\Form\ChfType;
use App\Repository\AccountRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Repository\PaginatorEnum;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        BalanceUpdaterFactoryInterface              $walletFactory,
        private BalanceUpdaterAccountInterface      $walletUpdater,
        private readonly AccountRepositoryInterface $chfRepository,
    ) {
        $this->walletUpdater = $walletFactory->create();
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
        $internalTransferOwner = $contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
        $chf->setContractor($internalTransferOwner);

        return $this->upsert($chf, $request);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'chf_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chf $chf): RedirectResponse|Response
    {
        return $this->upsert($chf, $request);
    }

    #[Route('/isconsistent/{id}/{boolAsString}', name: 'chf_is_consistent', methods: ['POST'])]
    public function isConsistent(Request $request, Chf $chf, string $boolAsString = ''): RedirectResponse
    {
        if ($this->isCsrfTokenValid('is_consistent' . $chf->getId(), (string) $request->request->get('_token'))) {
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
        if ($this->isCsrfTokenValid('delete' . $chf->getId(), (string) $request->request->get('_token'))) {
            $chf->setAmount(0);
            $this->walletUpdater->compute($this->chfRepository, $chf->getId());
            $this->chfRepository->remove($chf, true);
        }

        return $this->redirectToRoute('chf_index');
    }

    #[Route('/check', name: 'chf_check', methods: ['GET'])]
    public function check(
        BalanceSupervisorInterface $supervisor,
        TranslatorInterface $translator,
    ): RedirectResponse {
        /** @var AbstractWallet[] $chfs */
        $chfs = $this->chfRepository->getAllRecords();
        $supervisor->setWallets($chfs);
        $generator = $supervisor->crawl($this->chfRepository);
        $caught = false;
        /** @var Chf $chf */
        foreach ($generator as $chf) {
            $this->addFlash('error', $chf->__toString());
            $caught = true;
        }
        if (false === $caught) {
            $this->addFlash('success', $translator->trans('Passed'));
        }

        return $this->redirectToRoute('chf_index');
    }

    /**
     * @param Chf $chf
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    private function upsert(Chf $chf, Request $request): Response|RedirectResponse
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
}
