<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChfWalletControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testNew(): void
    {
        $this->client->request('GET', '/en/backup/paymentsByMonth');
        $this->assertSelectorTextContains('td#chfBalance', '70.07');

        $this->client->request('GET', '/en/chf');
        $this->assertSelectorTextContains('td#chf_balance1', '70.07');

        $this->client->request('GET', '/en/chf/new');
        $this->client->submitForm('Save', [
            'chf[amount]' => '20',
        ]);
        $this->assertSelectorTextContains('td#chf_balance1', '90.07');

        $this->client->request('GET', '/en/backup/paymentsByMonth');
        $this->assertSelectorTextContains('td#chfBalance', '90.07');
    }
}
