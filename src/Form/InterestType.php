<?php

namespace App\Form;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Type;

class InterestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $paramsArray = [
            'currency' => 'PLN',
            'required' => false,
            'constraints' => [
                new Type(['type' => "float"]),
                new GreaterThan(0.00),
            ]
        ];

        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'data' => new DateTime('first day of'),
            ])
            ->add('retiring_tax', MoneyType::class, array_merge(
                [
                    'label' => 'Backup\'s Tax',
                    'attr' => [
                        'autofocus' => true,
                        'required' => true
                    ],
                ],
                $paramsArray
            ))
            ->add('retiring', MoneyType::class, array_merge(['label' => 'Backup'], $paramsArray))
            ->add('holiday_tax', MoneyType::class, array_merge(['label' => 'Holiday\'s Tax'], $paramsArray))
            ->add('holiday', MoneyType::class, array_merge(['label' => 'Holiday'], $paramsArray))
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'save btn btn-primary  my-3',
                ],
            ]);
    }
}
