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
        $this->assertSame('-20', $values["wallet[amount]"]);
        $form['wallet[amount]']->setValue(-40);
        $this->client->submit($form);
        $this->assertSelectorTextContains('td#wallet_balance1', '150');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $this->assertSelectorTextContains('td#wallet_balance1', '170');
        $this->assertSelectorTextContains('td#wallet_amount1', '-20');
        $this->assertSelectorTextContains('td#wallet_balance2', '190');
        $this->assertSelectorTextContains('td#wallet_amount2', '-10');

        $this->client->submit(
            $crawler->filter('form#wallet_delete2')->form()
        );
        $this->assertSelectorTextContains('td#wallet_balance1', '180');
    }

    public function testChangeIsConsistent(): void
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
}
