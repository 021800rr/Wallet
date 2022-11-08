<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContractorControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testNew(): void
    {
        $this->client->request('GET', '/en/contractor/new');
        $this->client->submitForm('Save', [
            'contractor[description]' => 'Jakiś nowy odbiorca',
            'contractor[account]' => '1234567'
        ]);
        $this->client->request('GET', '/en/contractor');
        $this->assertSelectorTextContains('td#description2', 'Jakiś nowy odbiorca');
        $this->assertSelectorTextContains('td#account2', '1234567');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/contractor');

        $crawler = $this->client->click(
            $crawler->filter('a#contractor_edit2')->link()
        );
        $form = $crawler->selectButton('contractor_save')->form();
        $values = $form->getValues();
        $this->assertSame('Media Expert', $values["contractor[description]"]);
        $form['contractor[description]']->setValue('Media Expert xxx');
        $this->client->submit($form);
        $this->assertSelectorTextContains('td#description2', 'Media Expert xxx');
    }

    public function testDelete(): void
    {
        $this->client->request('GET', '/en/contractor/new');
        $this->client->submitForm('Save', [
            'contractor[description]' => 'Jakiś nowy odbiorca',
            'contractor[account]' => '1234567'
        ]);

        $this->client->request('GET', '/en/contractor');
        $crawler = $this->client->request('GET', '/en/contractor');
        $this->assertSelectorTextContains('td#description2', 'Jakiś nowy odbiorca');
        $this->assertSelectorTextContains('td#description3', 'Media Expert');

        $this->client->submit(
            $crawler->filter('form#contractor_delete2')->form()
        );
        $this->assertSelectorTextContains('td#description2', 'Media Expert');
        $this->assertSelectorTextContains('td#description3', 'Netflix');
    }
}
