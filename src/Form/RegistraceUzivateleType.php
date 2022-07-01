<?php

namespace App\Form;

use App\Entity\Uzivatel;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistraceUzivateleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jmeno', TextType::class, [
                "label" => "jméno:",
                "constraints" => [
                    new NotBlank(["message" => "Jméno nesmí zůstat prázdné."])
                ]
            ])
            ->add('password', RepeatedType::class, [
                "type" => PasswordType::class,
                "invalid_message" => "Hesla se musí shodovat",
                "constraints" => [
                    new NotBlank(["message" => "Heslo nesmí zůstat prázdné."])
                ],
                "first_options" => [
                    "label" => "heslo"
                ],
                "second_options" => [
                    "label" => "potvrzení hesla"
                ]
            ])
            ->add("odeslat", SubmitType::class, [
                "label" => "registrovat",
                "attr" => [
                    "class" => "btn-lg btn-primary"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Uzivatel::class,
            "attr" => [
                "novalidate" => "novalidate"
            ],
            "constraints" => [
                new UniqueEntity([
                    "fields" => "jmeno",
                    "message" => "Uživatel s tímto jménem již existuje"
                ])
            ],
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'iojjijsd'
        ]);
    }
}
