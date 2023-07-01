<?php

namespace App\Tests\Controller;

use App\Repository\ContractorRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeeControllerTest extends WebTestCase
{
    use ControllerSetup;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/fee');

        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount1', '-19.99');

        $this->assertSelectorTextContains('td#fee_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#fee_amount2', '-52');
    }

    public function testNew(): void
    {
        $this->client->request('GET', '/en/fee');

        $this->client->request('GET', '/en/fee/new');
        $this->client->submitForm('Save', [
            'fee[date]' => '20',
            'fee[amount]' => '-21',
            'fee[contractor]' => $this->contractor->getId(),
        ]);

        $this->assertSelectorTextContains('td#fee_contractor1', ContractorRepository::INTERNAL_TRANSFER);
        $this->assertSelectorTextContains('td#fee_amount1', '-21');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/fee');

        $crawler = $this->client->click(
            $crawler->filter('a#fee_edit1')->link()
        );

        $form = $crawler->selectButton('fee_save')->form();
        $values = $form->getValues();

        $this->assertSame('4', $values["fee[date]"]);
        $this->assertSame('-19.99', $values["fee[amount]"]);

        $this->client->submitForm('fee_save', [
            'fee[date]' => '10',
            'fee[amount]' => '-20',
        ]);

        $this->assertSelectorTextContains('td#fee_date1', '10');
        $this->assertSelectorTextContains('td#fee_amount1', '-20');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/fee');
        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_contractor2', 'Netflix');
        $this->client->submit(
            $crawler->filter('form#fee_delete1')->form()
        );
        $this->assertSelectorTextContains('td#fee_contractor1', 'Netflix');
    }

    public function testInsert(): void
    {
        $crawler = $this->client->request('GET', '/en/fee');

        $crawler = $this->client->submit(
            $crawler->selectButton('Save')->form()
        );
        $this->assertSame("http://localhost/en/wallet/", $crawler->getUri());

        $this->assertSelectorTextContains('td#wallet_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#wallet_balance1', '98.01');
        $this->assertSelectorTextContains('td#wallet_amount1', '-19.99');

        $this->assertSelectorTextContains('td#wallet_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#wallet_balance2', '118');
        $this->assertSelectorTextContains('td#wallet_amount2', '-52');

        $this->assertSelectorTextContains('td#wallet_contractor3', 'Allegro');
        $this->assertSelectorTextContains('td#wallet_balance3', '170');
        $this->assertSelectorTextContains('td#wallet_amount3', '-20');
    }
}
