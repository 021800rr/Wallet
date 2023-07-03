<?php

namespace App\Form;

use App\Entity\Contractor;
use App\Entity\Pln;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlnType extends AbstractAccountType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pln::class,
        ]);
    }
}
