<?php

namespace App\Form;

use App\Entity\Chf;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChfType extends AbstractAccountType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('amount', MoneyType::class, [
                'currency' => 'CHF',
                'attr' => [
                    'autofocus' => true,
                    'required' => true,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chf::class,
        ]);
    }
}
