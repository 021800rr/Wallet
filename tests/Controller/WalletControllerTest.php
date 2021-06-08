<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WalletControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testNew(): void
    {
        $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170,00');

        $this->client->clickLink('New Receipt');
        $this->client->submitForm('Save', [
            'wallet[amount]' => '-60',
        ]);
        $this->assertSelectorTextContains('td#wallet_balance1', '110,00');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170,00');

        $this->client->click(
            $crawler->filter('a#wallet_edit1')->link()
        );
        $this->assertFormValue('form', 'wallet[amount]', '-20');
        $this->client->submitForm('Save', [
            'wallet[amount]' => '-40',
        ]);
        $this->assertSelectorTextContains('td#wallet_balance1', '150,00');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170,00');
        $this->assertSelectorTextContains('td#wallet_amount1', '-20,00');
        $this->assertSelectorTextContains('td#wallet_balance2', '190,00');
        $this->assertSelectorTextContains('td#wallet_amount2', '-10,00');

        $this->client->submit(
            $crawler->filter('form#wallet_delete1')->form()
        );
        $this->assertSelectorTextContains('td#wallet_balance1', '190,00');
    }

    public function testChangeIsConsistent(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $imgUri = $crawler
            ->filter('form#wallet_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/question.png", $imgUri[0]);

        $crawler = $this->client->submit(
            $crawler->filter('form#wallet_is_consistent1')->form()
        );

        $imgUri = $crawler
            ->filter('form#wallet_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/ok.png", $imgUri[0]);
    }
}
