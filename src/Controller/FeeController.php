<?php

namespace App\Controller;

use App\Entity\Fee;
use App\Form\FeeType;
use App\Handler\FeeHandler;
use App\Repository\FeeRepositoryInterface;
use App\Repository\PaginatorEnum;
use App\Service\FixedFees\FixedFeesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{_locale}/fee',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class FeeController extends AbstractController
{
    public function __construct(private readonly FeeRepositoryInterface $feeRepository)
    {
    }

    #[Route('/', name: 'fee_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->feeRepository->getPaginator($offset);

        return $this->render('fee/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - PaginatorEnum::PerPage->value,
            'next' => min(count($paginator), $offset + PaginatorEnum::PerPage->value),
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
     * @param Fee $fee
     * @param Request $request
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
