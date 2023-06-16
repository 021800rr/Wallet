<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BackupControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/backup');
        $this->assertSelectorTextContains('td#backup_amount1', '300');
        $this->assertSelectorTextContains('td#backup_amount2', '200');
        $this->assertSelectorTextContains('td#backup_balance1', '600');
        $this->assertSelectorTextContains('td#backup_balance2', '300');
        $this->assertSelectorTextContains('td#backup_retiring1', '300');
        $this->assertSelectorTextContains('td#backup_retiring2', '150');
        $this->assertSelectorTextContains('td#backup_holiday1', '300');
        $this->assertSelectorTextContains('td#backup_holiday2', '150');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/backup');
        $this->assertSelectorTextContains('td#backup_balance1', '600');

        $this->client->click(
            $crawler->filter('#backup_edit1')
                ->link()
        );
        $this->client->submitForm('Save', [
            'backup[amount]' => '-60',
        ]);
        $this->assertSelectorTextContains('td#backup_balance1', '240');  // 300 (backup_balance2) - 60
        $this->assertSelectorTextContains('td#backup_retiring1', '150'); // === backup_retiring2
        $this->assertSelectorTextContains('td#backup_holiday1', '90');   // 150 (backup_holiday2) - 60
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/backup');
        $this->assertSelectorTextContains('td#backup_balance1', '600');

        $this->client->submit(
            $crawler->filter('form#backup_delete2')->form() // - 200
        );
        $this->assertSelectorTextContains('td#backup_balance1', '400');
    }

    public function testPaymentsByMonth(): void
    {
        $this->client->request('GET', '/en/backup/paymentsByMonth');
        $this->assertSelectorTextContains('td#backup_sum_of_amount1', '300');
        $this->assertSelectorTextContains('td#expected', '300');
        $this->assertSelectorTextContains('td#chfBalance', '70.07');
        $this->assertSelectorTextContains('td#eurBalance', '70.07');
        $this->assertSelectorTextContains('td#walletBalance', '170');
        $this->assertSelectorTextContains('td#holiday', '300');
        $this->assertSelectorTextContains('td#retiring', '300');
        $this->assertSelectorTextContains('td#balance', '600');
        $this->assertSelectorTextContains('td#total', '770');
    }

    public function testInterest(): void
    {
        $crawler = $this->client->request('GET', '/en/backup/interest');
        $form = $crawler->selectButton('interest_save')->form();
        $form['interest[retiring_tax]'] = '10';
        $form['interest[retiring]'] = '100';
        $form['interest[holiday_tax]'] = '1';
        $form['interest[holiday]'] = '10';
        $this->client->submit($form);
        $this->assertSelectorTextContains('td#backup_amount1', '99');
        $this->assertSelectorTextContains('td#backup_balance1', '699');
        $this->assertSelectorTextContains('td#backup_retiring1', '390');
        $this->assertSelectorTextContains('td#backup_holiday1', '309');
    }
}
