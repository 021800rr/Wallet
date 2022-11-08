<?php

namespace App\Service\Interest;

use Symfony\Component\Form\FormInterface;

interface InterestInterface
{
    public function form2Backup(FormInterface $form);
}
