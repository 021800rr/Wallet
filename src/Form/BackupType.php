<?php

namespace App\Form;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Validator\Unchangeable;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackupType extends AbstractAccountType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'disabled' => true,
                'constraints' => [
                    new Unchangeable(),
                ],
            ])
            ->add('amount', MoneyType::class, [
                'currency' => 'PLN',
                'attr' => [
                    'autofocus' => true,
                    'required' => true,
                ],
            ])
            ->add('contractor', EntityType::class, [
                'class' => Contractor::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.description', 'ASC');
                },
                'choice_label' => 'description',
                'disabled' => true,
                'constraints' => [
                    new Unchangeable(),
                ],
            ])
            ->add('balance', MoneyType::class, [
                'currency' => 'PLN',
                'disabled' => true,
                'constraints' => [
                    new Unchangeable(),
                ],
            ])
            ->add('retiring', MoneyType::class, [
                'currency' => 'PLN',
                'disabled' => true,
                'constraints' => [
                    new Unchangeable(),
                ],
            ])
            ->add('holiday', MoneyType::class, [
                'currency' => 'PLN',
                'disabled' => true,
                'constraints' => [
                    new Unchangeable(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Backup::class,
        ]);
    }
}
