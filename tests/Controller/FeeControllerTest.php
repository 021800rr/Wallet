<?php

namespace App\Tests\Controller;

use App\Repository\ContractorRepository;
use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeeControllerTest extends WebTestCase
{
    use SetUp;

    public function testIndex(): void
    {
        $this->webClient->request('GET', '/en/fee');

        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount1', '-19.99');

        $this->assertSelectorTextContains('td#fee_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#fee_amount2', '-52');
    }

    public function testNew(): void
    {
        $this->webClient->request('GET', '/en/fee');

        $this->webClient->request('GET', '/en/fee/new');
        $this->webClient->submitForm('Save', [
            'fee[date]' => '20',
            'fee[amount]' => '-21',
            'fee[contractor]' => $this->internalTransferOwner->getId(),
        ]);

        $this->assertSelectorTextContains('td#fee_contractor1', ContractorRepository::INTERNAL_TRANSFER);
        $this->assertSelectorTextContains('td#fee_amount1', '-21');
    }

    public function testEdit(): void
    {
        $crawler = $this->webClient->request('GET', '/en/fee');

        $this->webClient->click(
            $crawler->filter('a#fee_edit1')->link()
        );

        $this->webClient->submitForm('fee_save', [
            'fee[date]' => '10',
            'fee[amount]' => '-20',
        ]);

        $this->assertSelectorTextContains('td#fee_date1', '10');
        $this->assertSelectorTextContains('td#fee_amount1', '-20');
    }

    public function testDelete(): void
    {
        $crawler = $this->webClient->request('GET', '/en/fee');
        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_contractor2', 'Netflix');
        $this->webClient->submit(
            $crawler->filter('form#fee_delete1')->form()
        );
        $spotify = $this->contractorRepository->findOneBy(['description' => 'Spotify']);
        $this->assertNull(
            $this->feeRepository->findOneBy(['contractor' => $spotify])
        );
        $this->assertSelectorTextContains('td#fee_contractor1', 'Netflix');
    }

    public function testInsert(): void
    {
        $crawler = $this->webClient->request('GET', '/en/fee');

        $crawler = $this->webClient->submit(
            $crawler->selectButton('Save')->form()
        );
        $this->assertSame("http://localhost/en/pln/", $crawler->getUri());

        $this->assertSelectorTextContains('td#pln_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#pln_balance1', '28.01');
        $this->assertSelectorTextContains('td#pln_amount1', '-19.99');

        $this->assertSelectorTextContains('td#pln_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#pln_balance2', '48');
        $this->assertSelectorTextContains('td#pln_amount2', '-52');

        $this->assertSelectorTextContains('td#pln_contractor3', 'Allegro');
        $this->assertSelectorTextContains('td#pln_balance3', '100');
        $this->assertSelectorTextContains('td#pln_amount3', '-40');
    }
}
