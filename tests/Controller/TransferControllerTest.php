<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransferControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170,00');

        $this->client->clickLink('Backup');
        $this->assertSelectorTextContains('td#backup_balance1', '600,00');
        $this->assertSelectorTextContains('td#backup_retiring1', '300,00');
        $this->assertSelectorTextContains('td#backup_holiday1', '300,00');

        $crawler = $this->client->clickLink('Transfer');
        $form = $crawler->filter('button#transfer_to_backup_save')->form();
        $form['transfer_to_backup[amount]'] = '100';
        $crawler = $this->client->submit($form);

        $this->assertSame("http://localhost/en/backup/", $crawler->getUri());
        $this->assertSelectorTextContains('td#backup_balance1', '700,00');
        $this->assertSelectorTextContains('td#backup_retiring1', '350,00');
        $this->assertSelectorTextContains('td#backup_holiday1', '350,00');

        $this->client->clickLink('Wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '70,00');

        $crawler = $this->client->clickLink('Transfer');
        $form = $crawler->filter('button#transfer_to_wallet_save')->form();
        $form['transfer_to_wallet[amount]'] = '100';
        $crawler = $this->client->submit($form);

        $this->assertSame("http://localhost/en/wallet/", $crawler->getUri());
        $this->assertSelectorTextContains('td#wallet_balance1', '170,00');

        $this->client->clickLink('Backup');
        $this->assertSelectorTextContains('td#backup_balance1', '600,00');
        $this->assertSelectorTextContains('td#backup_retiring1', '350,00');
        $this->assertSelectorTextContains('td#backup_holiday1', '250,00');
    }
}
