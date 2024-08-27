<?php

namespace App\Tests\Controller;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContractorControllerTest extends WebTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testIndex(): void
    {
        $this->webClient->request('GET', '/en/contractor');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td#description1', 'Allegro');
        $this->assertSelectorTextContains('td#description2', 'Media Expert');
        $this->assertSelectorTextContains('td#description3', 'Netflix');
    }

    public function testNew(): void
    {
        $this->webClient->request('GET', '/en/contractor/new');
        $this->webClient->submitForm('Save', [
            'contractor[description]' => 'CCC',
            'contractor[account]' => '1234567'
        ]);
        $this->webClient->request('GET', '/en/contractor');
        $this->assertSelectorTextContains('td#description2', 'CCC');
        $this->assertSelectorTextContains('td#account2', '1234567');
    }

    public function testEdit(): void
    {
        $crawler = $this->webClient->request('GET', '/en/contractor');
        $this->webClient->click(
            $crawler->filter('a#contractor_edit2')
                ->link()
        );

        $this->webClient->submitForm('contractor_save', [
            'contractor[description]' => 'Media Expert xxx',
        ]);

        $this->assertSelectorTextContains('td#description2', 'Media Expert xxx');
    }

    public function testDelete(): void
    {
        $this->webClient->request('GET', '/en/contractor/new');
        $this->webClient->submitForm('Save', [
            'contractor[description]' => 'CCC',
            'contractor[account]' => '1234567'
        ]);

        $crawler = $this->webClient->request('GET', '/en/contractor');
        $this->assertSelectorTextContains('td#description2', 'CCC');

        $this->webClient->submit(
            $crawler->filter('form#contractor_delete2')
                ->form()
        );
        $this->assertNull($this->contractorRepository->findOneBy(['description' => 'CCC']));
        $this->assertSelectorTextContains('td#description2', 'Media Expert');
    }

    public function testDeleteWith(): void
    {
        $crawler = $this->webClient->request('GET', '/en/contractor');
        $this->webClient->submit(
            $crawler->filter('form#contractor_delete5')
                ->form()
        );
        $this->assertSelectorTextContains('div.alert-danger', 'Cannot delete contractor');
    }
}
