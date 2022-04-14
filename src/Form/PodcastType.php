<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Podcasts;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PodcastType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('rating')
            ->add('views')
            ->add('file')
            ->add('image', FileType::class , [ 'mapped'=> false])
            ->add('categorie', EntityType::class,[
                'class'=>Categorie::class,
                'choice_label'=>'namecateg'
            ])
            ->add('Submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Podcasts::class,
        ]);
    }
}
