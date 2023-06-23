<?php

namespace App\Tests\Controller;

use App\Repository\ContractorRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeeControllerTest extends WebTestCase
{
    use Setup;

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

        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount1', '-19.99');

        $this->assertSelectorTextContains('td#fee_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#fee_amount2', '-52');

        $this->client->request('GET', '/en/fee/new');
        $this->client->submitForm('Save', [
            'fee[date]' => '20',
            'fee[amount]' => '-21',
            'fee[contractor]' => $this->contractor->getId(),
        ]);

        $this->assertSelectorTextContains('td#fee_contractor1', ContractorRepository::INTERNAL_TRANSFER);
        $this->assertSelectorTextContains('td#fee_amount1', '-21');

        $this->assertSelectorTextContains('td#fee_contractor2', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount2', '-19.99');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/fee');

        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount1', '-19.99');

        $crawler = $this->client->click(
            $crawler->filter('a#fee_edit1')->link()
        );

        $form = $crawler->selectButton('fee_save')->form();
        $values = $form->getValues();

        $this->assertSame('-19.99', $values["fee[amount]"]);
        $form['fee[amount]']->setValue('-19.9');
        $this->assertSame('4', $values["fee[date]"]);
        $form['fee[date]']->setValue('14');

        $this->client->submit($form);

        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount1', '-19.9');
        $this->assertSelectorTextContains('td#fee_date1', '14');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/fee');

        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount1', '-19.99');

        $this->assertSelectorTextContains('td#fee_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#fee_amount2', '-52');

        $this->client->submit(
            $crawler->filter('form#fee_delete1')->form()
        );

        $this->assertSelectorTextContains('td#fee_contractor1', 'Netflix');
        $this->assertSelectorTextContains('td#fee_amount1', '-52');
    }

    public function testInsert(): void
    {
        $this->client->request('GET', '/en/wallet');

        $this->assertSelectorTextContains('td#wallet_contractor1', 'Allegro');
        $this->assertSelectorTextContains('td#wallet_balance1', '170');
        $this->assertSelectorTextContains('td#wallet_amount1', '-20');

        $crawler = $this->client->request('GET', '/en/fee');

        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount1', '-19.99');

        $this->assertSelectorTextContains('td#fee_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#fee_amount2', '-52');

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
