<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Review;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Review',TextType::class,[
                'label'=>'Review',
                'attr'=>['placeholder'=>'Your Review',
                    'class'=>'form-control','id'=>'message','cols'=>30,'rows'=>10],
            ])
           /* ->add('user',EntityType::class,[
                'label'=>'User',
                'class'=>User::class,
                'choice_label'=>'username'
            ])
            ->add('Event',EntityType::class,[
                'label'=>'Event',
                'class'=>Event::class,
                'choice_label'=>'name'
            ])*/

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
