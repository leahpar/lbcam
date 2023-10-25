<?php

namespace App\Form;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{
    // https://symfony.com/doc/current/form/data_transformers.html#about-model-and-view-transformers
    // https://grafikart.fr/tutoriels/tags-form-type-882

    public function __construct(
        private readonly EntityManagerInterface $em,
    ){}

    public function transform($value): string
    {
        return implode(',', $value);
    }

    public function reverseTransform($value): array
    {
        $names = array_unique(array_filter(array_map('trim', explode(',', $value))));
        $tags = $this->em->getRepository(Tag::class)->findBy([
            'nom' => $names
        ]);
        $newNames = array_diff($names, $tags);
        foreach ($newNames as $name) {
            $tag = new Tag();
            $tag->nom = $name;
            $tags[] = $tag;
        }
        return $tags;
    }

}
