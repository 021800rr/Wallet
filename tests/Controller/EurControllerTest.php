<?php

namespace App\Tests\Controller;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EurControllerTest extends WebTestCase
{
    use SetUp;

    public function testIndex(): void
    {
        $this->kernelBrowser->request('GET', '/en/eur');

        $this->assertSelectorTextContains('td#eur_amount1', '40.04');
        $this->assertSelectorTextContains('td#eur_balance1', '70.07');

        $this->assertSelectorTextContains('td#eur_amount2', '20.02');
        $this->assertSelectorTextContains('td#eur_balance2', '30.03');

        $this->assertSelectorTextContains('td#eur_amount3', '10.01');
        $this->assertSelectorTextContains('td#eur_balance3', '10.01');
    }

    public function testNewInvalidAmountType(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/eur/new');
        $form = $crawler->selectButton('Save')->form();

        $form['eur[amount]'] = 'invalid';
        $this->kernelBrowser->submit($form);

        $this->assertSelectorExists('input[name="eur[amount]"].is-invalid');

        $this->assertSelectorTextContains('.invalid-feedback.d-block', 'Please enter a valid money amount.');
    }

    public function testNew(): void
    {
        $this->kernelBrowser->request('GET', '/en/eur/new');
        $this->kernelBrowser->submitForm('Save', [
            'eur[amount]' => '50.05',
        ]);
        $this->assertSelectorTextContains('td#eur_balance1', '120.12');

        $this->kernelBrowser->request('GET', '/en/backup/payments-by-month');
        $this->assertSelectorTextContains('td#eurBalance', '120.12');
    }

    public function testEdit(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/eur');
        $this->kernelBrowser->click(
            $crawler->filter('a#eur_edit1')->link()
        );

        $this->kernelBrowser->submitForm('eur_save', [
            'eur[amount]' => '1',
        ]);

        $this->assertSelectorTextContains('td#eur_balance1', '31.03');
    }

    public function testDelete(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/eur');
        $this->kernelBrowser->submit(
            $crawler->filter('form#eur_delete1')->form()
        );
        $this->assertSelectorTextContains('td#eur_balance1', '30.03');
    }

    public function testIsConsistent(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/eur');

        $crawler = $this->kernelBrowser->submit(
            $crawler->filter('form#eur_is_consistent1')->form()
        );

        $imgUri = $crawler
            ->filter('form#eur_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/ok.png", $imgUri[0]);
    }
}
