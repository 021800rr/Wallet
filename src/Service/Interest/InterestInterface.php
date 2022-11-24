<?php

namespace App\Service\Interest;

use App\Entity\Backup;
use Symfony\Component\Form\FormInterface;

interface InterestInterface
{
    public function form2Backup(FormInterface $form): Backup;
}
