<?php

namespace App\Tests\Controller;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    use SetUp;

    public function testIndex(): void
    {
        $this->webClient->request('GET', '/en/pln');
        $this->webClient->submitForm('form_search', [
            'form[query]' => '190',
        ]);

        $this->assertSelectorTextContains('td#search_balance1', '190');

        $this->webClient->request('GET', '/en/pln');
        $this->webClient->submitForm('form_search', [
            'form[query]' => '-10',
        ]);

        $this->assertSelectorTextContains('td#search_amount1', '-10');

        $this->webClient->request('GET', '/en/pln');
        $this->webClient->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
    }

    public function testChangeIsConsistent(): void
    {
        $this->webClient->request('GET', '/en/pln');
        $crawler = $this->webClient->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $crawler = $this->webClient->submit(
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
        $this->webClient->request('GET', '/en/pln');
        $crawler = $this->webClient->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->webClient->click(
            $crawler->filter('a#pln_edit1')->link()
        );
        $this->webClient->submitForm('pln_save', [
            'pln[amount]' => '-50',
        ]);
        $this->assertSelectorTextContains('td#search_balance1', '90');
    }

    public function testDelete(): void
    {
        $crawler = $this->webClient->request('GET', '/en/pln');
        $crawler->selectButton('form_search')->form();
        $crawler = $this->webClient->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->webClient->submit(
            $crawler->filter('form#pln_delete2')->form()
        );
        $this->assertSelectorTextContains('td#search_balance1', '130');
    }
}
