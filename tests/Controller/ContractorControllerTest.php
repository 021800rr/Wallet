<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContractorControllerTest extends WebTestCase
{
    use ControllerSetup;

    private KernelBrowser $client;

    public function testIndex(): void
    {
        $this->client->request('GET', '/en/contractor');
        $this->assertSelectorTextContains('td#description1', 'Allegro');
        $this->assertSelectorTextContains('td#description2', 'Media Expert');
        $this->assertSelectorTextContains('td#description3', 'Netflix');
    }
    public function testNew(): void
    {
        $this->client->request('GET', '/en/contractor/new');
        $this->client->submitForm('Save', [
            'contractor[description]' => 'CCC',
            'contractor[account]' => '1234567'
        ]);
        $this->client->request('GET', '/en/contractor');
        $this->assertSelectorTextContains('td#description2', 'CCC');
        $this->assertSelectorTextContains('td#account2', '1234567');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/contractor');
        $this->client->click(
            $crawler->filter('a#contractor_edit2')->link()
        );

        $this->client->submitForm('contractor_save', [
            'contractor[description]' => 'Media Expert xxx',
        ]);

        $this->assertSelectorTextContains('td#description2', 'Media Expert xxx');
    }

    public function testDelete(): void
    {
        $this->client->request('GET', '/en/contractor/new');
        $this->client->submitForm('Save', [
            'contractor[description]' => 'CCC',
            'contractor[account]' => '1234567'
        ]);

        $crawler = $this->client->request('GET', '/en/contractor');
        $this->assertSelectorTextContains('td#description2', 'CCC');

        $this->client->submit(
            $crawler->filter('form#contractor_delete2')->form()
        );
        $this->assertSelectorTextContains('td#description2', 'Media Expert');
    }
}
