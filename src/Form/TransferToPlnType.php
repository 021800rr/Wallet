<?php

namespace App\Form;

use App\Entity\Pln;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferToPlnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'currency' => 'PLN',
                'label' => 'PlnWallet',
                'attr' => [
                    'required' => true
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'save btn btn-primary  my-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pln::class,
        ]);
    }
}
