<?php

namespace App\Controller;

use App\Entity\Contractor;
use App\Form\ContractorType;
use App\Repository\ContractorRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    path: '/{_locale}/contractor',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class ContractorController extends AbstractAppPaginator
{
    public function __construct(private readonly ContractorRepositoryInterface $contractorRepository)
    {
    }

    #[Route('/', name: 'contractor_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('contractor/index.html.twig', [
            'pager' => $this->getPagerfanta(
                $request,
                $this->contractorRepository->getAllRecordsQueryBuilder(),
            ),
        ]);
    }

    #[Route('/new', name: 'contractor_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $contractor = new Contractor();

        return $this->upsert($contractor, $request);
    }

    #[Route('/edit/{id}', name: 'contractor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contractor $contractor): RedirectResponse|Response
    {
        return $this->upsert($contractor, $request);
    }

    #[Route('/delete/{id}', name: 'contractor_delete', methods: ['POST'])]
    public function delete(Request $request, Contractor $contractor): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $contractor->getId(), (string) $request->request->get('_token'))) {
            $this->contractorRepository->remove($contractor, true);
        }

        return $this->redirectToRoute('contractor_index');
    }

    /**
     * @return RedirectResponse|Response
     */
    private function upsert(Contractor $contractor, Request $request): Response|RedirectResponse
    {
        $form = $this->createForm(ContractorType::class, $contractor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contractorRepository->save($contractor, true);

            return $this->redirectToRoute('contractor_index');
        }

        return $this->render('contractor/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
