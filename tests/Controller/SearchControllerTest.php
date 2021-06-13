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
        $form['form[query]']->setValue('all');
        $this->client->submit($form);

        $i = 1;
        do {
            $this->assertSelectorTextContains('td#search_contractor' . $i, 'Allegro');
            $lastFoundIndex = $i;
            $i++;
        } while ($i < 3);
        $this->assertSame(2, $lastFoundIndex);
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
}
