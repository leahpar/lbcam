<?php

namespace App\TwigComponent;

use App\Entity\Truc;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(name:'tags', template: 'components/tags.html.twig')]
class TagsComponent
{

    public string $type = 'populaires';
    public array $tags;

    public function __construct(
        private readonly EntityManagerInterface $em
    ){}

    public function mount()
    {
        $this->tags = $this->em->getRepository(Truc::class)->getTagsCpt();
    }

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults(['type' => 'populaires']);
        $resolver->setAllowedValues('type', ['populaires']);
        $resolver->setRequired('type');

        return $resolver->resolve($data);
    }
}
