<?php

namespace App\Tests\Exception;

use Exception;

class InternalTransferOwnerNotFoundException extends Exception
{
    // @phpstan-ignore-next-line
    protected $message = 'No internal transfer owner found.';
}
