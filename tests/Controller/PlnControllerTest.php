<?php

namespace App\Tests\Controller;

use App\Tests\SetupController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlnControllerTest extends WebTestCase
{
    use SetupController;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/pln');

        $this->assertSelectorTextContains('td#pln_amount1', '-20');
        $this->assertSelectorTextContains('td#pln_balance1', '170');

        $this->assertSelectorTextContains('td#pln_amount2', '-10');
        $this->assertSelectorTextContains('td#pln_balance2', '191');
    }

    public function testNew(): void
    {
        $this->client->request('GET', '/en/pln');
        $this->client->clickLink('New Receipt');
        $this->client->submitForm('Save', [
            'pln[amount]' => '-70',
        ]);
        $this->assertSelectorTextContains('td#pln_balance1', '100');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/pln');

        $this->client->click(
            $crawler->filter('a#pln_edit1')->link()
        );
        $this->client->submitForm('pln_save', [
            'pln[amount]' => '-40',
        ]);
        $this->assertSelectorTextContains('td#pln_balance1', '151');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/pln');

        $this->client->submit(
            $crawler->filter('form#pln_delete2')->form()
        );
        $this->assertSelectorTextContains('td#pln_balance1', '180');
    }

    public function testIsConsistent(): void
    {
        $crawler = $this->client->request('GET', '/en/pln');

        $crawler = $this->client->submit(
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
        $this->client->request('GET', '/en/pln');
        $this->client->clickLink('Check');
        $this->assertSelectorTextContains('div.alert-danger', '2 : 2021-05-12 : -10 : 191 : 190 : Allegro');
        $this->assertSelectorTextContains('td.td-danger', 'A different balance value is expected: 171');
    }

    public function testBalanceAdjustment(): void
    {
        // Potrzebne są dwie operacje na rekordzie z błędem:
        // - najpierw, zwiększymy o pewną wartość kwotę wydatków,
        //   w ten sposób bilans zostanie ponownie wyliczony i zniknie błąd obliczeń;
        // - następnie, w tym samym rekordzie zmniejszymy kwotę wydatków o taką samą wartość,
        //   czyli wrócimy do pierwotnej wartości poniesionych obciążeń;
        //   drugie przeliczenie niczego nie zepsuje.

        // A: Tymczasowo zwiększ wydatki o jeden, chodzi tylko o ponowne przeliczenie salda.

        $crawler = $this->client->request('GET', '/en/pln');
        $this->assertSelectorTextContains('td#pln_balance2', '191');

        $crawler = $this->client->click(
            $crawler->filter('a#pln_edit2')->link()
        );
        $form = $crawler->selectButton('pln_save')->form();
        $values = $form->getValues();
        $this->assertSame('-10.00', $values["pln[amount]"]);
        $this->client->submitForm('pln_save', [
            'pln[amount]' => '-11',
        ]);
        $this->assertSelectorTextContains('td#pln_balance2', '189');

        // B: Zmniejsz wydatki do pierwotnej wartości, ponowne przeliczenie niczego nie zepsuje.

        $crawler = $this->client->request('GET', '/en/pln');
        $this->assertSelectorTextContains('td#pln_balance2', '189');

        $crawler = $this->client->click(
            $crawler->filter('a#pln_edit2')->link()
        );
        $form = $crawler->selectButton('pln_save')->form();
        $values = $form->getValues();
        $this->assertSame('-11.00', $values["pln[amount]"]);
        $this->client->submitForm('pln_save', [
            'pln[amount]' => '-10',
        ]);
        $this->assertSelectorTextContains('td#pln_balance2', '190');

        // Sprawdź, czy błąd został usunięty.

        $this->client->request('GET', '/en/pln');
        $this->client->clickLink('Check');
        $this->assertSelectorTextContains('div.alert-success', 'Passed');
    }
}
