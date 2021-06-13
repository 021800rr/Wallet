<?php

namespace App\Form;

use App\Entity\Contractor;
use App\Entity\Fee;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', null, [
                'attr' => [
                    'autofocus' => true,
                    'required' => true
                ]
            ])
            ->add('amount')
            ->add('contractor', EntityType::class, [
                'class' => Contractor::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.description', 'ASC');
                },
                'choice_label' => 'description',
            ])
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
            'data_class' => Fee::class,
        ]);
    }
}
