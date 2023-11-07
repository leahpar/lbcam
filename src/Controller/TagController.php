<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Truc;
use App\Search\TagSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{

    #[Route('/tags', name: 'tags_list')]
    public function addTag(Request $request, EntityManagerInterface $em): Response
    {
        $serach = new TagSearch($request->query->all());
        $tags = $em->getRepository(Tag::class)->search($serach);

        return $this->json([
            "results" => array_map(fn(Tag $tag) => [
                "value" => $tag->nom,
                "text" => $tag->nom,
            ], $tags->getIterator()->getArrayCopy()),
        ]);
    }

    #[Route('/tags/populaires', name: 'tags_populaires')]
    public function nuageTag(EntityManagerInterface $em): Response
    {
        $tags = $em->getRepository(Truc::class)->getTagsCpt();
        return $this->render('truc/tags_populaires.html.twig', [
            'tags' => $tags,
        ]);
    }

}
