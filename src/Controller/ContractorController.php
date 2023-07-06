<?php

namespace App\Controller;

use App\Entity\Contractor;
use App\Form\ContractorType;
use App\Repository\ContractorRepositoryInterface;
use App\Repository\PaginatorEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{_locale}/contractor',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class ContractorController extends AbstractController
{
    public function __construct(private readonly ContractorRepositoryInterface $contractorRepository)
    {
    }

    #[Route('/', name: 'contractor_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->contractorRepository->getPaginator($offset);

        return $this->render('contractor/index.html.twig', [
            'paginator' => $paginator,
            'previous' => $offset - PaginatorEnum::PerPage->value,
            'next' => min(count($paginator), $offset + PaginatorEnum::PerPage->value),
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
     * @param Contractor $contractor
     * @param Request $request
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
