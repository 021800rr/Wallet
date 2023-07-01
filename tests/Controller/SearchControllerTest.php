<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    use ControllerSetup;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/wallet');
        $this->client->submitForm('form_search', [
            'form[query]' => '191',
        ]);

        $this->assertSelectorTextContains('td#search_balance1', '191');

        $this->client->request('GET', '/en/wallet');
        $this->client->submitForm('form_search', [
            'form[query]' => '-10',
        ]);

        $this->assertSelectorTextContains('td#search_amount1', '-10');

        $this->client->request('GET', '/en/wallet');
        $this->client->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
    }

    public function testChangeIsConsistent(): void
    {
        $this->client->request('GET', '/en/wallet');
        $crawler = $this->client->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $imgUri = $crawler
            ->filter('form#search_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/question.png", $imgUri[0]);

        $crawler = $this->client->submit(
            $crawler->filter('form#search_is_consistent1')->form()
        );

        $imgUri = $crawler
            ->filter('form#search_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/ok.png", $imgUri[0]);
    }

    public function testEdit(): void
    {
        $this->client->request('GET', '/en/wallet');
        $crawler = $this->client->submitForm('form_search', [
            'form[query]' => 'all',
        ]);
        $this->assertSelectorTextContains('td#search_balance1', '170');

        $crawler = $this->client->click(
            $crawler->filter('a#wallet_edit1')->link()
        );
        $form = $crawler->selectButton('wallet_save')->form();
        $values = $form->getValues();
        $this->assertSame('-20.00', $values["wallet[amount]"]);
        $this->client->submitForm('wallet_save', [
            'wallet[amount]' => '-40',
        ]);
        $this->assertSelectorTextContains('td#search_balance1', '151');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $crawler->selectButton('form_search')->form();
        $crawler = $this->client->submitForm('form_search', [
            'form[query]' => 'all',
        ]);
        $this->assertSelectorTextContains('td#search_amount1', '-20');
        $this->assertSelectorTextContains('td#search_balance1', '170');

        $this->assertSelectorTextContains('td#search_amount2', '-10');
        $this->assertSelectorTextContains('td#search_balance2', '191');

        $this->client->submit(
            $crawler->filter('form#wallet_delete2')->form()
        );
        $this->assertSelectorTextContains('td#search_balance1', '180');
    }
}
