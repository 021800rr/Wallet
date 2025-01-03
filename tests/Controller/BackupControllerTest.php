<?php

namespace App\Tests\Controller;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BackupControllerTest extends WebTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testIndex(): void
    {
        $this->webClient->request('GET', '/en/backup');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td#backup_amount1', '300');
        $this->assertSelectorTextContains('td#backup_holiday1', '300');
        $this->assertSelectorTextContains('td#backup_retiring1', '300');
        $this->assertSelectorTextContains('td#backup_balance1', '600');

        $this->assertSelectorTextContains('td#backup_amount2', '200');
        $this->assertSelectorTextContains('td#backup_holiday2', '150');
        $this->assertSelectorTextContains('td#backup_retiring2', '150');
        $this->assertSelectorTextContains('td#backup_balance2', '300');

        $this->assertSelectorTextContains('td#backup_amount3', '100');
        $this->assertSelectorTextContains('td#backup_holiday3', '50');
        $this->assertSelectorTextContains('td#backup_retiring3', '50');
        $this->assertSelectorTextContains('td#backup_balance3', '100');
    }

    /**
     * @dataProvider editDataProvider
     */
    public function testEdit(string $amount, string $expectedHoliday, string $expectedRetiring, string $expectedBalance): void
    {
        $crawler = $this->webClient->request('GET', '/en/backup');
        $this->webClient->click(
            $crawler->filter('#backup_edit1')
                ->link()
        );
        $this->webClient->submitForm('Save', [
            'backup[amount]' => $amount,
        ]);
        $this->assertSelectorTextContains('td#backup_holiday1', $expectedHoliday);
        $this->assertSelectorTextContains('td#backup_retiring1', $expectedRetiring);
        $this->assertSelectorTextContains('td#backup_balance1', $expectedBalance);
    }

    /**
     * @return array<string, string[]>
     */
    public function editDataProvider(): array
    {
        return [
            'positive amount' => ['400', '350', '350', '700'],
            'negative amount' => ['-100', '50', '150', '200'],
            'zero amount' => ['0', '150', '150', '300'],
            'high amount' => ['10000', '5150', '5150', '10300'],
        ];
    }

    public function testEditWithInvalidAmount(): void
    {
        $crawler = $this->webClient->request('GET', '/en/backup');
        $this->webClient->click(
            $crawler->filter('#backup_edit1')
                ->link()
        );
        $this->webClient->submitForm('Save', [
            'backup[amount]' => 'abc',
        ]);

        $this->assertSelectorExists('input[name="backup[amount]"].is-invalid');
        $this->assertSelectorTextContains('.invalid-feedback.d-block', 'Please enter a valid money amount.');
    }

    public function testDelete(): void
    {
        $crawler = $this->webClient->request('GET', '/en/backup');
        $this->webClient->submit(
            $crawler->filter('form#backup_delete1')->form()
        );
        $this->assertSelectorTextContains('td#backup_balance1', '300');
    }

    public function testPaymentsByMonth(): void
    {
        $this->webClient->request('GET', '/en/backup/payments-by-month');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td#backup_sum_of_amounts1', '300');
        $this->assertSelectorTextContains('td#expected', '300');
        $this->assertSelectorTextContains('td#chfBalance', '70.07');
        $this->assertSelectorTextContains('td#eurBalance', '70.07');
        $this->assertSelectorTextContains('td#plnBalance', '100');
        $this->assertSelectorTextContains('td#holiday', '300');
        $this->assertSelectorTextContains('td#retiring', '300');
        $this->assertSelectorTextContains('td#balance', '600');
        $this->assertSelectorTextContains('td#total', '700');
    }

    public function testInterest(): void
    {
        $crawler = $this->webClient->request('GET', '/en/backup/interest');
        $form = $crawler->selectButton('interest_save')
            ->form();
        $form['interest[retiring_tax]'] = '10';
        $form['interest[retiring]'] = '100';
        $form['interest[holiday_tax]'] = '1';
        $form['interest[holiday]'] = '10';
        $this->webClient->submit($form);
        $this->assertSelectorTextContains('td#backup_amount1', '99');
        $this->assertSelectorTextContains('td#backup_balance1', '699');
        $this->assertSelectorTextContains('td#backup_retiring1', '390');
        $this->assertSelectorTextContains('td#backup_holiday1', '309');
    }

    public function testRenderEditForm(): void
    {
        $crawler = $this->webClient->request('GET', '/en/backup/edit/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="backup"]');
    }

    public function testEditFormValidation(): void
    {
        $crawler = $this->webClient->request('GET', '/en/backup/edit/1');
        $form = $crawler->selectButton('Save')->form();

        $form['backup[amount]'] = '';

        $this->webClient->submit($form);
        $this->assertSelectorExists('input[name="backup[amount]"].is-invalid');
        $this->assertSelectorTextContains('.invalid-feedback.d-block', 'This value should not be null.');
    }

    public function testRenderInterestForm(): void
    {
        $crawler = $this->webClient->request('GET', '/en/backup/interest');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="interest"]');
    }

    public function testInterestFormValidation(): void
    {
        $crawler = $this->webClient->request('GET', '/en/backup/interest');
        $form = $crawler->selectButton('interest_save')->form();

        $form['interest[retiring_tax]'] = '';
        $form['interest[retiring]'] = '';

        $this->webClient->submit($form);
        $this->assertSelectorExists('input[name="interest[retiring_tax]"].is-invalid');
        $this->assertSelectorExists('input[name="interest[retiring]"].is-invalid');
        $this->assertSelectorTextContains('.invalid-feedback.d-block', 'This value should not be blank.');
    }
}
