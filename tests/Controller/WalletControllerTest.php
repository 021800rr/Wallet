<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WalletControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testNew(): void
    {
        $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170');

        $this->client->clickLink('New Receipt');
        $this->client->submitForm('Save', [
            'wallet[amount]' => '-60',
        ]);
        $this->assertSelectorTextContains('td#wallet_balance1', '110');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170');

        $crawler = $this->client->click(
            $crawler->filter('a#wallet_edit1')->link()
        );
        $form = $crawler->selectButton('wallet_save')->form();
        $values = $form->getValues();
        $this->assertSame('-20.00', $values["wallet[amount]"]);
        $form['wallet[amount]']->setValue('-40');
        $this->client->submit($form);
        $this->assertSelectorTextContains('td#wallet_balance1', '151');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170');
        $this->assertSelectorTextContains('td#wallet_amount1', '-20');
        $this->assertSelectorTextContains('td#wallet_balance2', '191');
        $this->assertSelectorTextContains('td#wallet_amount2', '-10');

        $this->client->submit(
            $crawler->filter('form#wallet_delete2')->form()
        );
        $this->assertSelectorTextContains('td#wallet_balance1', '180');
    }

    public function testIsConsistent(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $imgUri = $crawler
            ->filter('form#wallet_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/question.png", $imgUri[0]);

        $crawler = $this->client->submit(
            $crawler->filter('form#wallet_is_consistent1')->form()
        );

        $imgUri = $crawler
            ->filter('form#wallet_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/ok.png", $imgUri[0]);
    }

    public function testCheck(): void
    {
        $this->client->request('GET', '/en/wallet');
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

        $crawler = $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance2', '191');

        $crawler = $this->client->click(
            $crawler->filter('a#wallet_edit2')->link()
        );
        $form = $crawler->selectButton('wallet_save')->form();
        $values = $form->getValues();
        $this->assertSame('-10.00', $values["wallet[amount]"]);
        $form['wallet[amount]']->setValue('-11');
        $this->client->submit($form);
        $this->assertSelectorTextContains('td#wallet_balance2', '189');

        // B: Zmniejsz wydatki do pierwotnej wartości, ponowne przeliczenie niczego nie zepsuje.

        $crawler = $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance2', '189');

        $crawler = $this->client->click(
            $crawler->filter('a#wallet_edit2')->link()
        );
        $form = $crawler->selectButton('wallet_save')->form();
        $values = $form->getValues();
        $this->assertSame('-11.00', $values["wallet[amount]"]);
        $form['wallet[amount]']->setValue('-10');
        $this->client->submit($form);
        $this->assertSelectorTextContains('td#wallet_balance2', '190');

        // Sprawdź, czy błąd został usunięty.

        $this->client->request('GET', '/en/wallet');
        $this->client->clickLink('Check');
        $this->assertSelectorTextContains('div.alert-success', 'Passed');
    }
}
