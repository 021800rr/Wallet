<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EuroWalletControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testNew(): void
    {
        $this->client->request('GET', '/en/backup/paymentsByMonth');
        $this->assertSelectorTextContains('td#eurBalance', '70.07');

        $this->client->request('GET', '/en/eur');
        $this->assertSelectorTextContains('td#eur_balance1', '70.07');

        $this->client->request('GET', '/en/eur/new');
        $this->client->submitForm('Save', [
            'eur[amount]' => '20',
        ]);
        $this->assertSelectorTextContains('td#eur_balance1', '90.07');

        $this->client->request('GET', '/en/backup/paymentsByMonth');
        $this->assertSelectorTextContains('td#eurBalance', '90.07');
    }
}
