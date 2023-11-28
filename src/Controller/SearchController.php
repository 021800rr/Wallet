<?php

namespace App\Controller;

use App\Entity\Pln;
use App\Repository\PlnSearchRepositoryInterface;
use App\Service\OffsetQuery\OffsetHelperInterface;
use App\Service\OffsetQuery\QueryHelperInterface;
use App\Service\RequestParser\RequestParserInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route(
    path: '/{_locale}/search',
    requirements: [
        '_locale' => 'pl|en',
    ],
    locale: 'pl',
)]
#[IsGranted('ROLE_ADMIN')]
class SearchController extends AbstractAppPaginator
{
    #[Route('/', name: 'search_index')]
    public function index(string $query = ''): Response
    {
        return $this->render('search/index.html.twig', [
            'form' => $this->getForm($query)->createView()
        ]);
    }

    #[Route('/result', name: 'search_result')]
    public function search(
        Request                      $request,
        PlnSearchRepositoryInterface $plnRepository,
        QueryHelperInterface         $queryHelper,
        OffsetHelperInterface        $offsetHelper,
        RequestParserInterface       $requestParser,
    ): Response {
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string, string> $data */
            $data = $form->getData();
            $query = $data['query'];

            $queryHelper->setQuery($query);
            $offsetHelper->resetOffset();
        } else {
            /** @var string $query */
            [$query] = $requestParser->strategy(SearchController::class, $request);
        }

        return $this->render('search/result.html.twig', [
            'query' => $query,
            'pager' => $this->getPagerfanta(
                $request,
                $plnRepository->search($query),
            ),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'search_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pln $pln, PlnController $plnController): Response
    {
        return $plnController->edit($request, $pln, 'search_result');
    }

    #[Route('/is-consistent/{id}/{boolAsString}', name: 'search_is_consistent', methods: ['POST'])]
    public function isConsistent(
        Request       $request,
        Pln           $pln,
        PlnController $plnController,
        string        $boolAsString = '',
    ): Response {
        return $plnController->isConsistent($request, $pln, $boolAsString, 'search_result');
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'search_delete', methods: ['POST'])]
    public function delete(Request $request, Pln $pln, PlnController $plnController): Response
    {
        return $plnController->delete($request, $pln, 'search_result');
    }

    private function getForm(string $query = ''): FormInterface
    {
        return $this->createFormBuilder(
            ['query' => $query],
            ['action' => $this->generateUrl('search_result')]
        )
            ->add(
                'query',
                SearchType::class,
                ['constraints' => new NotBlank()]
            )
            ->add('search', SubmitType::class)
            ->getForm()
        ;
    }
}
