<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $form = $crawler->selectButton('form_search')->form();
        $form['form[query]']->setValue('191');
        $this->client->submit($form);

        $this->assertSelectorTextContains('td#search_balance1', '191');

        $crawler = $this->client->request('GET', '/en/wallet');
        $form = $crawler->selectButton('form_search')->form();
        $form['form[query]']->setValue('-10');
        $this->client->submit($form);

        $this->assertSelectorTextContains('td#search_amount1', '-10');

        $crawler = $this->client->request('GET', '/en/wallet');
        $form = $crawler->selectButton('form_search')->form();
        $form['form[query]']->setValue('all');
        $this->client->submit($form);

        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
        $this->assertSelectorTextContains('td#search_contractor1', 'Allegro');
    }

    public function testChangeIsConsistent(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $form = $crawler->selectButton('form_search')->form();
        $form['form[query]']->setValue('all');
        $crawler = $this->client->submit($form);

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
        $crawler = $this->client->request('GET', '/en/wallet');
        $form = $crawler->selectButton('form_search')->form();
        $form['form[query]']->setValue('all');
        $crawler = $this->client->submit($form);
        $this->assertSelectorTextContains('td#search_balance1', '170');

        $crawler = $this->client->click(
            $crawler->filter('a#wallet_edit1')->link()
        );
        $form = $crawler->selectButton('wallet_save')->form();
        $values = $form->getValues();
        $this->assertSame('-20.00', $values["wallet[amount]"]);
        $form['wallet[amount]']->setValue('-40');
        $this->client->submit($form);
        $this->assertSelectorTextContains('td#search_balance1', '151');
    }

    public function testDelete(): void
    {
        $crawler = $this->client->request('GET', '/en/wallet');
        $form = $crawler->selectButton('form_search')->form();
        $form['form[query]']->setValue('all');
        $crawler = $this->client->submit($form);

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
