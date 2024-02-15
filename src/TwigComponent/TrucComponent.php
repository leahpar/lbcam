<?php

namespace App\TwigComponent;

use App\Entity\Truc;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(name:'truc', template: 'truc/truc.html.twig')]
class TrucComponent
{

    public Truc $truc;

    public function __construct(
        private readonly Security $security
    ){}

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setRequired('truc');
        $resolver->setAllowedTypes('truc', Truc::class);
        return $resolver->resolve($data);
    }

    #[ExposeInTemplate]
    public function isProprietaire(): bool
    {
        return $this->truc->user->getId() == $this->security->getUser()->getId();
    }

}
