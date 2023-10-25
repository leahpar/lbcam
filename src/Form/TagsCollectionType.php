<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TagsCollectionType extends AbstractType
{

    public function __construct(
        private readonly TagsTransformer $tagsTransformer,
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new CollectionToArrayTransformer(), true)
            ->addModelTransformer($this->tagsTransformer, true)
        ;
    }

    public function getParent()
    {
        return TextType::class;
    }
}
