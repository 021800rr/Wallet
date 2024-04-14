<?php

namespace App\Controller;

use App\Entity\Fee;
use App\Form\FeeType;
use App\Handler\FeeHandler;
use App\Repository\FeeRepositoryInterface;
use App\Service\FixedFees\FixedFeesInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    path: '/{_locale}/fee',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class FeeController extends AbstractAppPaginator
{
    public function __construct(private readonly FeeRepositoryInterface $feeRepository)
    {
    }

    #[Route('/', name: 'fee_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('fee/index.html.twig', [
            'pager' => $this->getPagerfanta(
                $request,
                $this->feeRepository->getAllRecordsQueryBuilder(),
            ),
        ]);
    }

    #[Route('/new', name: 'fee_new', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $fee = new Fee();

        return $this->upsert($fee, $request);
    }

    #[Route('/edit/{id}', name: 'fee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fee $fee): RedirectResponse|Response
    {
        return $this->upsert($fee, $request);
    }

    #[Route('/delete/{id}', name: 'fee_delete', methods: ['POST'])]
    public function delete(Request $request, Fee $fee): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $fee->getId(), (string) $request->request->get('_token'))) {
            $this->feeRepository->remove($fee, true);
        }

        return $this->redirectToRoute('fee_index');
    }

    #[Route('/insert', name: 'fee_insert_to_pln', methods: ['POST'])]
    public function insert(
        Request                $request,
        FixedFeesInterface     $fixedFees,
        FeeHandler             $feeHandler,
    ): RedirectResponse|Response {
        if ($this->isCsrfTokenValid('fixedfees', (string) $request->request->get('_token'))) {
            $feeHandler->handle($fixedFees);

            return $this->redirectToRoute('pln_index');
        }

        return $this->redirectToRoute('fee_index');
    }

    /**
     * @return RedirectResponse|Response
     */
    private function upsert(Fee $fee, Request $request): Response|RedirectResponse
    {
        $form = $this->createForm(FeeType::class, $fee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->feeRepository->save($fee, true);

            return $this->redirectToRoute('fee_index');
        }

        return $this->render('fee/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
