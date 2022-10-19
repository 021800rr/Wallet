<?php

namespace App\Form;

use App\Entity\Contractor;
use App\Entity\Wallet;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WalletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('amount', MoneyType::class, [
                'currency' => 'PLN',
                'attr' => [
                    'autofocus' => true,
                    'required' => true,
                ]
            ])
            ->add('contractor', EntityType::class, [
                'class' => Contractor::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.description', 'ASC');
                },
                'choice_label' => 'description',
            ])
            ->add('description')
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'save btn btn-primary  my-3',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wallet::class,
        ]);
    }
}
