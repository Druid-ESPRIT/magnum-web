<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name',TextType::class,[
                'label' => 'Event Name',
                'empty_data'=>''
            ])
            ->add('Description',TextType::class,[
                'label' => 'Description',
                'empty_data'=>''
            ])
            ->add('Type',ChoiceType::class,[
                'label' => 'Type',
                'choices'  => [
                    'LIVE' => "LIVE",
                    'On-Site' => "On-Site",
                ],
            ])
            ->add('Location',TextType::class)
            ->add('Date',DateType::class,[
                'label' => 'Date',
                'empty_data'=>''
            ])
            ->add('MaxParticipants',TextType::class,[
                'label' => 'Number Of Participants',
                'empty_data'=>''
            ])
            ->add('Payant',CheckboxType::class,[
                'label' => 'Paid',
            ])
            ->add('Prix',NumberType::class,[
                'label' => 'Price',
                'empty_data'=>0
            ])
            /*->add('Status',ChoiceType::class,[
                'label' => 'Status',
                'choices'  => [
                    'Finished' => "Finished",
                    'Not Finished' => "Not Finished",
                ],
            ])*/
            ->add('Image',FileType::class,[
                'label' => 'Picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5Mi',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ])
           /* ->add('User',EntityType::class,[
                'class'=>User::class,
                'choice_label'=>'username'
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            ''
        ]);
    }
}
