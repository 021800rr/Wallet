<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use App\Service\OffsetQuery\OffsetInterface;
use App\Service\OffsetQuery\QueryInterface;
use App\Service\RequestParser\RequestInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
class SearchController extends AbstractController
{
    #[Route('/', name: 'search_index')] // , methods: ['GET'])]
    public function index(string $query = ''): Response
    {
        return $this->render('search/index.html.twig', [
            'form' => $this->formHelper($query)->createView()
        ]);
    }

    #[Route('/result', name: 'search_result')] // , methods: ['POST'])]
    public function search(
        Request $request,
        WalletRepository $walletRepository,
        QueryInterface $queryHelper,
        OffsetInterface $offsetHelper,
        RequestInterface $parser
    ): Response {
        $form = $this->formHelper();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $query = $data['query'];

            $queryHelper->set($query);
            $offsetHelper->reset();
            $offset = 0;
        } else {
            list($query, $offset) = $parser->strategy(SearchController::class, $request);
        }
        $paginator = $walletRepository->search($query, $offset);

        return $this->render('search/result.html.twig', [
            'query' => $query,
            'paginator' => $paginator,
            'previous' => $offset - WalletRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + WalletRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/edit/{id}', name: 'search_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Wallet $wallet, WalletController $controller): Response
    {
        return $controller->edit($request, $wallet, 'search_result');
    }

    #[Route('/isconsistent/{id}/{bool}', name: 'search_is_consistent', methods: ['POST'])]
    public function isConsistent(
        Request $request,
        Wallet $wallet,
        WalletController $controller,
        string $bool = ''
    ): Response {
        return $controller->isConsistent($request, $wallet, $bool, 'search_result');
    }

    #[Route('/delete/{id}', name: 'search_delete', methods: ['POST'])]
    public function delete(Request $request, Wallet $wallet, WalletController $controller): Response
    {
        return $controller->delete($request, $wallet, 'search_result');
    }

    private function formHelper(string $query = ''): FormInterface
    {
        return $this->createFormBuilder([
            'query' => $query
        ], [
            'action' => $this->generateUrl('search_result'),
        ])
            ->add('query', SearchType::class, [
                'constraints' => new NotBlank(),
            ])
            ->add('search', SubmitType::class)
            ->getForm();
    }
}
