<?php

namespace App\Tests\Controller;

use App\Tests\SetupController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    use SetupController;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/pln');
        $this->client->submitForm('form_search', [
            'form[query]' => '190',
        ]);

        $this->assertSelectorTextContains('td#search_balance1', '190');

        $this->client->request('GET', '/en/pln');
        $this->client->submitForm('form_search', [
            'form[query]' => '-10',
        ]);

        $this->assertSelectorTextContains('td#search_amount1', '-10');

        $this->client->request('GET', '/en/pln');
        $this->client->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
    }

    public function testChangeIsConsistent(): void
    {
        $this->client->request('GET', '/en/pln');
        $crawler = $this->client->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

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
        $this->client->request('GET', '/en/pln');
        $crawler = $this->client->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->client->click(
            $crawler->filter('a#pln_edit1')->link()
        );
        $this->client->submitForm('pln_save', [
            'pln[amount]' => '-50',
        ]);
        $this->assertSelectorTextContains('td#search_balance1', '90');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/pln');
        $crawler->selectButton('form_search')->form();
        $crawler = $this->client->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->client->submit(
            $crawler->filter('form#pln_delete2')->form()
        );
        $this->assertSelectorTextContains('td#search_balance1', '130');
    }
}
