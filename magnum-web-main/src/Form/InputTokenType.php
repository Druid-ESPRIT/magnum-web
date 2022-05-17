<?php
namespace App\Form;

use App\Entity\Tokens;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class InputTokenType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add("token", TextType::class, [
                "required" => true,
                "label" => "Have a look at your email, we sent you a token!",
                "attr" => ["class" => "form-control", "placeholder" => "My token"],
            ])
            ->add("send", SubmitType::class, [
                "attr" => ["class" => "btn btn-primary"],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Tokens::class,
        ]);
    }
}
