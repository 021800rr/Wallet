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

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/eur');
        $this->assertSelectorTextContains('td#eur_balance1', '70.07');

        $crawler = $this->client->click(
            $crawler->filter('a#eur_edit1')->link()
        );
        $form = $crawler->selectButton('eur_save')->form();
        $values = $form->getValues();
        $this->assertSame('40.04', $values["eur[amount]"]);
        $form['eur[amount]']->setValue('20');
        $this->client->submit($form);
        $this->assertSelectorTextContains('td#eur_balance1', '50.03');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/eur');
        $this->assertSelectorTextContains('td#eur_balance1', '70.07');
        $this->assertSelectorTextContains('td#eur_amount1', '40.04');
        $this->assertSelectorTextContains('td#eur_balance2', '30.03');
        $this->assertSelectorTextContains('td#eur_amount2', '20.02');

        $this->client->submit(
            $crawler->filter('form#eur_delete2')->form()
        );
        $this->assertSelectorTextContains('td#eur_balance1', '50.05');
    }

    public function testIsConsistent(): void
    {
        $crawler = $this->client->request('GET', '/en/eur');
        $imgUri = $crawler
            ->filter('form#eur_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/question.png", $imgUri[0]);

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
