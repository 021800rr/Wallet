<?php

namespace App\Tests\Controller;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChfControllerTest extends WebTestCase
{
    use SetUp;

    public function testIndex(): void
    {
        $this->kernelBrowser->request('GET', '/en/chf');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td#chf_amount1', '40.04');
        $this->assertSelectorTextContains('td#chf_balance1', '70.07');

        $this->assertSelectorTextContains('td#chf_amount2', '20.02');
        $this->assertSelectorTextContains('td#chf_balance2', '30.03');

        $this->assertSelectorTextContains('td#chf_amount3', '10.01');
        $this->assertSelectorTextContains('td#chf_balance3', '10.01');
    }

    /**
     * @dataProvider amountDataProvider
     */
    public function testNew(string $amount, string $expectedBalance): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/chf/new');
        $form = $crawler->selectButton('Save')
            ->form();
        $form['chf[amount]'] = $amount;
        $this->kernelBrowser->submit($form);

        $this->assertSelectorTextContains('td#chf_balance1', $expectedBalance);

        $this->kernelBrowser->request('GET', '/en/backup/payments-by-month');
        $this->assertSelectorTextContains('td#chfBalance', $expectedBalance);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function amountDataProvider(): array
    {
        return [
            ['50.05', '120.12'],
            ['0', '70.07'],
            ['-10.00', '60.07'],
        ];
    }

    public function testNewWithInvalidAmount(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/chf/new');
        $form = $crawler->selectButton('Save')
            ->form();
        $form['chf[amount]'] = 'abc';
        $this->kernelBrowser->submit($form);

        $this->assertSelectorTextContains('.invalid-feedback.d-block', 'Please enter a valid money amount.');
    }

    /**
     * @dataProvider editDataProvider
     */
    public function testEdit(string $amount, string $expectedBalance): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/chf');
        $this->kernelBrowser->click(
            $crawler->filter('a#chf_edit1')->link()
        );

        $this->kernelBrowser->submitForm('chf_save', [
            'chf[amount]' => $amount,
        ]);

        $this->assertSelectorTextContains('td#chf_balance1', $expectedBalance);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function editDataProvider(): array
    {
        return [
            ['1', '31.03'],
            ['0', '30.03'],
            ['-10', '20.03'],
        ];
    }

    public function testEditWithInvalidAmount(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/chf');
        $this->kernelBrowser->click(
            $crawler->filter('a#chf_edit1')
                ->link()
        );

        $this->kernelBrowser->submitForm('chf_save', [
            'chf[amount]' => 'abc',
        ]);

        $this->assertSelectorTextContains('.invalid-feedback.d-block', 'Please enter a valid money amount.');
    }

    public function testDelete(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/chf');
        $this->kernelBrowser->submit(
            $crawler->filter('form#chf_delete1')
                ->form()
        );
        $this->assertSelectorTextContains('td#chf_balance1', '30.03');
    }

    public function testIsConsistent(): void
    {
        $crawler = $this->kernelBrowser->request('GET', '/en/chf');

        $crawler = $this->kernelBrowser->submit(
            $crawler->filter('form#chf_is_consistent1')
                ->form()
        );

        $imgUri = $crawler
            ->filter('form#chf_is_consistent1')
            ->filter('input.submitter')
            ->extract(['src']);
        $this->assertSame("/images/ok.png", $imgUri[0]);
    }
}
