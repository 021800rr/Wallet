<?php

namespace App\Tests\Controller;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    use SetUp;

    public function testIndex(): void
    {
        $this->kernelBrowser->request('GET', '/en/pln');
        $this->kernelBrowser->submitForm('form_search', [
            'form[query]' => '190',
        ]);

        $this->assertSelectorTextContains('td#search_balance1', '190');

        $this->kernelBrowser->request('GET', '/en/pln');
        $this->kernelBrowser->submitForm('form_search', [
            'form[query]' => '-10',
        ]);

        $this->assertSelectorTextContains('td#search_amount1', '-10');

        $this->kernelBrowser->request('GET', '/en/pln');
        $this->kernelBrowser->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
    }

    public function testChangeIsConsistent(): void
    {
        $this->kernelBrowser->request('GET', '/en/pln');
        $crawler = $this->kernelBrowser->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $crawler = $this->kernelBrowser->submit(
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
        $this->kernelBrowser->request('GET', '/en/pln');
        $crawler = $this->kernelBrowser->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->kernelBrowser->click(
            $crawler->filter('a#pln_edit1')->link()
        );
        $this->kernelBrowser->submitForm('pln_save', [
            'pln[amount]' => '-50',
        ]);
        $this->assertSelectorTextContains('td#search_balance1', '90');
    }

    public function testDelete(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/pln');
        $crawler->selectButton('form_search')->form();
        $crawler = $this->kernelBrowser->submitForm('form_search', [
            'form[query]' => 'all',
        ]);

        $this->kernelBrowser->submit(
            $crawler->filter('form#pln_delete2')->form()
        );
        $this->assertSelectorTextContains('td#search_balance1', '130');
    }
}
