<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeeControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170,00');
        $this->assertSelectorTextContains('td#wallet_contractor1', 'Allegro');
        $this->assertSelectorTextContains('td#wallet_amount1', '-20,00');

        $crawler = $this->client->request('GET', '/en/fee');

        $this->assertSelectorTextContains('td#fee_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#fee_amount1', '-19,99');

        $this->assertSelectorTextContains('td#fee_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#fee_amount2', '-52,00');

        $crawler = $this->client->submit(
            $crawler->selectButton('Save')->form()
        );
        $this->assertSame("http://localhost/en/wallet/", $crawler->getUri());

        $this->assertSelectorTextContains('td#wallet_balance1', '98,01');

        $this->assertSelectorTextContains('td#wallet_contractor3', 'Allegro');
        $this->assertSelectorTextContains('td#wallet_amount3', '-20,00');

        $this->assertSelectorTextContains('td#wallet_contractor1', 'Spotify');
        $this->assertSelectorTextContains('td#wallet_balance1', '98,01');
        $this->assertSelectorTextContains('td#wallet_amount1', '-19,99');

        $this->assertSelectorTextContains('td#wallet_contractor2', 'Netflix');
        $this->assertSelectorTextContains('td#wallet_balance2', '118,00');
        $this->assertSelectorTextContains('td#wallet_amount2', '-52,00');
    }
}
