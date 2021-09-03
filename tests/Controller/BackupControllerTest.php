<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BackupControllerTest extends WebTestCase
{
    use Setup;

    private KernelBrowser $client;

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', '/en/backup');
        $this->assertSelectorTextContains('td#backup_balance1', '600');

        $this->client->click(
            $crawler->filter('#1-edit')
                ->link()
        );
        $this->client->submitForm('Save', [
            'backup[amount]' => '-60',
        ]);
        $this->assertSelectorTextContains('td#backup_balance1', '240');
        $this->assertSelectorTextContains('td#backup_retiring1', '150');
        $this->assertSelectorTextContains('td#backup_holiday1', '90');
    }
}
