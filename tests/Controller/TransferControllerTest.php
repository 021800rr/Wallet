<?php

namespace App\Tests\Controller;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransferControllerTest extends WebTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testIndex(): void
    {
        $this->webClient->request('GET', '/en/pln');
        $this->assertSelectorTextContains('td#pln_balance1', '100');

        $this->webClient->clickLink('Backup');
        $this->assertSelectorTextContains('td#backup_balance1', '600');
        $this->assertSelectorTextContains('td#backup_retiring1', '300');
        $this->assertSelectorTextContains('td#backup_holiday1', '300');

        $crawler = $this->webClient->clickLink('Transfer');
        $form = $crawler->filter('button#transfer_to_backup_save')->form();
        $form['transfer_to_backup[amount]'] = '100';
        $this->webClient->submit($form);

        $this->webClient->request('GET', '/en/pln');
        $this->assertSelectorTextContains('td#pln_balance1', '0');

        $this->webClient->request('GET', '/en/backup');
        $this->assertSelectorTextContains('td#backup_balance1', '700');
        $this->assertSelectorTextContains('td#backup_retiring1', '350');
        $this->assertSelectorTextContains('td#backup_holiday1', '350');

        $crawler = $this->webClient->clickLink('Transfer');
        $form = $crawler->filter('button#transfer_to_pln_save')->form();
        $form['transfer_to_pln[amount]'] = '100';
        $this->webClient->submit($form);

        $this->webClient->request('GET', '/en/pln');
        $this->assertSelectorTextContains('td#pln_balance1', '100');

        $this->webClient->clickLink('Backup');
        $this->assertSelectorTextContains('td#backup_balance1', '600');
        $this->assertSelectorTextContains('td#backup_retiring1', '350');
        $this->assertSelectorTextContains('td#backup_holiday1', '250');
    }

    public function testCurrency(): void
    {
        $this->webClient->request('GET', '/en/pln');
        $crawler = $this->webClient->clickLink('Transfer');
        $form = $crawler->filter('button#transfer_to_backup_save')->form();
        $form['transfer_to_backup[amount]'] = '100';
        $form['transfer_to_backup[currency]'] = '1';
        $this->webClient->submit($form);

        $this->webClient->request('GET', '/en/pln');
        $this->assertSelectorTextContains('td#pln_balance1', '0');

        $this->webClient->request('GET', '/en/backup');
        $this->assertSelectorTextContains('td#backup_amount1', '100');
        $this->assertSelectorTextContains('td#backup_balance1', '0');
        $this->assertSelectorTextContains('td#backup_retiring1', '0');
        $this->assertSelectorTextContains('td#backup_holiday1', '0');
    }
}
