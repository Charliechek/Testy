<?php

namespace App\Form;

use App\Entity\Otazka;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class OtazkaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextType::class, [
                "label" => "text otázky",
                "constraints" => [
                    new NotBlank(["message" => "Otázka nesmí zůstat prázdná."])
                ]
            ])
            ->add("odpovedi", CollectionType::class, [
                "label" => false,
                "entry_type" => OdpovedType::class,
            ])
            ->add("spravnaOdpoved", ChoiceType::class, [
                "multiple" => false,
                "expanded" => true,
                "choices" => $options["data"]->getOdpovedi()->getKeys(),
                "constraints" => [
                    new NotBlank (["message" => "Musíte zvolit správnou odpověď."])
                ]
            ])
            ->add('odeslat', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Otazka::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'dfknfdks',
            "attr" => [
                "novalidate" => "novalidate"
            ]
        ]);
    }
}
