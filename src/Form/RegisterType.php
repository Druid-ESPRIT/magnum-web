<?php
namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('role', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Are you a podcaster?',
                "attr" => [
                    'class' => "form-control",
                ]
            ])
            ->add("username", TextType::class, [
                "label" => "Username",
                "attr" => [
                    "class" => "form-control",
                ],
            ])
            ->add("email", EmailType::class, [
                "label" => "Email",
                "attr" => ["class" => "form-control"],
            ])
            ->add("password", PasswordType::class, [
                "label" => "Password",
                "attr" => [
                    "class" => "form-control",
                ],
            ])
            ->add("avatar", FileType::class, [
                "label" => "Upload an avatar",
                "mapped" => true,
                "required" => false,
                "constraints" => [
                    new File([
                        "maxSize" => "4096k",
                        "mimeTypes" => ["image/png", "image/jpeg"],
                        "mimeTypesMessage" =>
                            "Please upload an image file (IMG, JPEG)",
                    ]),
                ],
            ])
            ->add("firstName", TextType::class, [
                "mapped" => false,
                "required" => false,
                "label" => "First Name",
                "label_attr" => [ "id" => "label_firstName" ],
                "attr" => [
                    "class" => "form-control",
                ],
            ])
            ->add("lastName", TextType::class, [
                "mapped" => false,
                "required" => false,
                "label" => "Last Name",
                "label_attr" => [ "id" => "label_lastName" ],
                "attr" => [
                    "class" => "form-control",
                ],
            ])
            ->add("biography", TextareaType::class, [
                "mapped" => false,
                "required" => false,
                "label" => "Biography",
                "label_attr" => [ "id" => "label_biography" ],
                "attr" => [
                    "class" => "form-control",
                ],
            ])
            ->add("register", SubmitType::class, [
                "attr" => ["class" => "btn oneMusic-btn mt-30"],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Users::class,
        ]);
    }
}
