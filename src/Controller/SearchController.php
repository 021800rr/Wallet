<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Route("/{_locale<%app.supported_locales%>}/search")
 * @IsGranted("ROLE_ADMIN")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/", name="search_index")
     */
    public function index(Request $request, string $query = ''): Response
    {
        $lastSearchedPhrase = [
            'query' => $query
        ];

        $form = $this->createFormBuilder($lastSearchedPhrase)
            ->add('query', SearchType::class, [
                'constraints' => new NotBlank(),
            ])
            ->add('search', SubmitType::class)
            ->getForm();

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/result", name="search_result"), methods={"POST"}
     */
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('query', SearchType::class)
            ->add('search', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        $query = $request->query->get('query') ?? '';
        if ($form->isSubmitted() && $form->isValid()) {
            $query = ($form->getData())['query'];
        }
        $offset = max(0, $request->query->getInt('offset', 0));
        $walletRepository = $entityManager->getRepository(Wallet::class);
        $paginator = $walletRepository->search($query, $offset);

        return $this->render('search/result.html.twig', [
            'query' => $query,
            'paginator' => $paginator,
            'previous' => $offset - WalletRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + WalletRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    /**
     * @Route("/isconsistent/{id}/{bool}", name="search_is_consistent", methods={"POST"})
     */
    public function isConsistent(Request $request, Wallet $search, string $bool = ''): Response
    {
        $query = '';
        if ($this->isCsrfTokenValid('is_consistent' . $search->getId(), $request->request->get('_token'))) {
            $query = $request->request->get('query') ?? '';
            switch ($bool) {
                case "true":
                    $search->setIsConsistent(true);
                    break;
                case "false":
                    $search->setIsConsistent(false);
                    break;
                default:
                    return $this->redirectToRoute('search_result', ['query' => $query]);
            }
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('search_result', ['query' => $query]);
    }
}
