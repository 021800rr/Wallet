<?php

namespace App\Tests\Controller;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlnControllerTest extends WebTestCase
{
    use SetUp;

    public function testIndex(): void
    {
        $this->webClient->request('GET', '/en/pln');

        $this->assertSelectorTextContains('td#pln_amount1', '-40');
        $this->assertSelectorTextContains('td#pln_balance1', '100');

        $this->assertSelectorTextContains('td#pln_amount2', '-30');
        $this->assertSelectorTextContains('td#pln_balance2', '140');
    }

    public function testNew(): void
    {
        $this->webClient->request('GET', '/en/pln');
        $this->webClient->clickLink('New Receipt');
        $this->webClient->submitForm('Save', [
            'pln[amount]' => '-50',
        ]);
        $this->assertSelectorTextContains('td#pln_balance1', '50');
    }

    public function testEdit(): void
    {
        $crawler = $this->webClient->request('GET', '/en/pln');

        $this->webClient->click(
            $crawler->filter('a#pln_edit1')->link()
        );
        $this->webClient->submitForm('pln_save', [
            'pln[amount]' => '-50',
        ]);
        $this->assertSelectorTextContains('td#pln_balance1', '90');
    }

    public function testEditMoveBackward(): void
    {
        $crawler = $this->webClient->request('GET', '/en/pln');

        $this->webClient->click(
            $crawler->filter('a#pln_edit3')->link()
        );
        $this->webClient->submitForm('pln_save', [
            'pln[date]' => '2021-02-12',
        ]);

        $this->webClient->request('GET', '/en/pln');

        $this->assertSelectorTextContains('td#pln_amount3', '-10');
        $this->assertSelectorTextContains('td#pln_balance3', '170');

        $this->assertSelectorTextContains('td#pln_amount4', '-20');
        $this->assertSelectorTextContains('td#pln_balance4', '180');

        $this->assertSelectorTextContains('td#pln_amount5', '-1');
        $this->assertSelectorTextContains('td#pln_balance5', '200');
    }

    public function testDelete(): void
    {
        $crawler = $this->webClient->request('GET', '/en/pln');

        $this->webClient->submit(
            $crawler->filter('form#pln_delete2')->form()
        );
        $this->assertSelectorTextContains('td#pln_balance1', '130');
    }

    public function testIsConsistent(): void
    {
        $crawler = $this->webClient->request('GET', '/en/pln');

        $crawler = $this->webClient->submit(
            $crawler->filter('form#pln_is_consistent1')->form()
        );

        $imgUri = $crawler
            ->filter('form#pln_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/ok.png", $imgUri[0]);
    }

    public function testCheck(): void
    {
        $this->webClient->request('GET', '/en/pln');
        $this->webClient->clickLink('Check');
        $this->assertSelectorTextContains('div.alert-success', 'Passed');
    }
}
