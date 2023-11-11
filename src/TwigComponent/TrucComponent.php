<?php

namespace App\TwigComponent;

use App\Entity\Truc;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(name:'truc', template: 'truc/truc.html.twig')]
class TrucComponent
{

    public Truc $truc;

    public function __construct(
        private readonly Security $security
    ){}

    #[ExposeInTemplate]
    public function isProprietaire(): bool
    {
        return $this->truc->user->getId() == $this->security->getUser()->getId();
    }

}
