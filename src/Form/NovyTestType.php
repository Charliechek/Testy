<?php

namespace App\Form;

use App\Entity\Test;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NovyTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nazev', TextType::class, [
                "label" => "název",
                "constraints" => [
                    new NotBlank(["message" => "Název nesmí být prázdný."]),
                ]
            ])
            ->add("odeslat", SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-primary"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'dfknfdks',
            "constraints" => [
                new UniqueEntity([
                    "fields" => "nazev", 
                    "message" => "Test s tímto názvem již existuje. Vyberte jiný."
                ]),
            ]
        ]);
    }
}
