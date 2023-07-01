<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Repository\PaginatorEnum;
use App\Repository\WalletRepositoryInterface;
use App\Service\OffsetQuery\OffsetHelperInterface;
use App\Service\OffsetQuery\QueryHelperInterface;
use App\Service\RequestParser\RequestParserInterface;
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
    #[Route('/', name: 'search_index')]
    public function index(string $query = ''): Response
    {
        return $this->render('search/index.html.twig', [
            'form' => $this->getForm($query)->createView()
        ]);
    }

    #[Route('/result', name: 'search_result')]
    public function search(
        Request                   $request,
        WalletRepositoryInterface $walletRepository,
        QueryHelperInterface      $queryHelper,
        OffsetHelperInterface     $offsetHelper,
        RequestParserInterface    $requestParser,
    ): Response {
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string, string> $data */
            $data = $form->getData();
            $query = $data['query'];

            $queryHelper->setQuery($query);
            $offsetHelper->resetOffset();
            $offset = 0;
        } else {
            /**
             * @var string $query
             * @var int $offset
             */
            [$query, $offset] = $requestParser->strategy(SearchController::class, $request);
        }
        $paginator = $walletRepository->search($query, $offset);

        return $this->render('search/result.html.twig', [
            'query' => $query,
            'paginator' => $paginator,
            'previous' => $offset - PaginatorEnum::PerPage->value,
            'next' => min(count($paginator), $offset + PaginatorEnum::PerPage->value),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'search_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Wallet $wallet, WalletController $walletController): Response
    {
        return $walletController->edit($request, $wallet, 'search_result');
    }

    #[Route('/isconsistent/{id}/{boolAsString}', name: 'search_is_consistent', methods: ['POST'])]
    public function isConsistent(
        Request          $request,
        Wallet           $wallet,
        WalletController $walletController,
        string           $boolAsString = '',
    ): Response {
        return $walletController->isConsistent($request, $wallet, $boolAsString, 'search_result');
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'search_delete', methods: ['POST'])]
    public function delete(Request $request, Wallet $wallet, WalletController $walletController): Response
    {
        return $walletController->delete($request, $wallet, 'search_result');
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
