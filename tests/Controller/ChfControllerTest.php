<?php

namespace App\Tests\Controller;

use App\Tests\SetupController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChfControllerTest extends WebTestCase
{
    use SetupController;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/chf');

        $this->assertSelectorTextContains('td#chf_amount1', '40.04');
        $this->assertSelectorTextContains('td#chf_balance1', '70.07');

        $this->assertSelectorTextContains('td#chf_amount2', '20.02');
        $this->assertSelectorTextContains('td#chf_balance2', '30.03');

        $this->assertSelectorTextContains('td#chf_amount3', '10.01');
        $this->assertSelectorTextContains('td#chf_balance3', '10.01');
    }

    public function testNew(): void
    {
        $this->client->request('GET', '/en/chf/new');
        $this->client->submitForm('Save', [
            'chf[amount]' => '50.05',
        ]);
        $this->assertSelectorTextContains('td#chf_balance1', '120.12');

        $this->client->request('GET', '/en/backup/paymentsByMonth');
        $this->assertSelectorTextContains('td#chfBalance', '120.12');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/chf');
        $this->client->click(
            $crawler->filter('a#chf_edit1')->link()
        );

        $this->client->submitForm('chf_save', [
            'chf[amount]' => '1',
        ]);

        $this->assertSelectorTextContains('td#chf_balance1', '31.03');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/chf');
        $this->client->submit(
            $crawler->filter('form#chf_delete2')->form()
        );
        $this->assertSelectorTextContains('td#chf_balance1', '50.05');
    }

    public function testIsConsistent(): void
    {
        $crawler = $this->client->request('GET', '/en/chf');

        $crawler = $this->client->submit(
            $crawler->filter('form#chf_is_consistent1')->form()
        );

        $imgUri = $crawler
            ->filter('form#chf_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/ok.png", $imgUri[0]);
    }
}
