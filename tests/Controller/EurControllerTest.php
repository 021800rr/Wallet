<?php

namespace App\Tests\Controller;

use App\Tests\SetupController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EurControllerTest extends WebTestCase
{
    use SetupController;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/eur');

        $this->assertSelectorTextContains('td#eur_amount1', '40.04');
        $this->assertSelectorTextContains('td#eur_balance1', '70.07');

        $this->assertSelectorTextContains('td#eur_amount2', '20.02');
        $this->assertSelectorTextContains('td#eur_balance2', '30.03');

        $this->assertSelectorTextContains('td#eur_amount3', '10.01');
        $this->assertSelectorTextContains('td#eur_balance3', '10.01');
    }

    public function testNew(): void
    {
        $this->client->request('GET', '/en/eur/new');
        $this->client->submitForm('Save', [
            'eur[amount]' => '50.05',
        ]);
        $this->assertSelectorTextContains('td#eur_balance1', '120.12');

        $this->client->request('GET', '/en/backup/payments-by-month');
        $this->assertSelectorTextContains('td#eurBalance', '120.12');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/eur');
        $this->client->click(
            $crawler->filter('a#eur_edit1')->link()
        );

        $this->client->submitForm('eur_save', [
            'eur[amount]' => '1',
        ]);

        $this->assertSelectorTextContains('td#eur_balance1', '31.03');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/eur');
        $this->client->submit(
            $crawler->filter('form#eur_delete1')->form()
        );
        $this->assertSelectorTextContains('td#eur_balance1', '30.03');
    }

    public function testIsConsistent(): void
    {
        $crawler = $this->client->request('GET', '/en/eur');

        $crawler = $this->client->submit(
            $crawler->filter('form#eur_is_consistent1')->form()
        );

        $imgUri = $crawler
            ->filter('form#eur_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/ok.png", $imgUri[0]);
    }
}
