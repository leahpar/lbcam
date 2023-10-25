<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Truc;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrucType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', Type\TextType::class, [
                'label' => 'Nom du truc',
                'required' => true,
            ])
            ->add('description', Type\TextareaType::class, [
                'label' => 'Description du truc',
                'required' => false,
            ])
            ->add('tags', TagsCollectionType::class, [
                'required' => false,
                'autocomplete' => true,
                'autocomplete_url' => '/tags',
                'tom_select_options' => [
                    'create' => true,
                ],
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Truc::class,
        ]);
    }
}
