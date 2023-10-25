<?php

namespace App\Controller;

use App\Entity\Truc;
use App\Form\TrucType;
use App\Search\TrucSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrucController extends AbstractController
{
    #[Route('/', name: 'trucs_list')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $trucs = $em->getRepository(Truc::class)->search(new TrucSearch($request->query->all()));

        return $this->render('truc/index.html.twig', [
            'trucs' => $trucs,
        ]);
    }

    #[Route('/nouveau-truc', name: 'trucs_add')]
    #[Route('/trucs/{slug}/modifier', name: 'truc_edit')]
    public function edit(Request $request, EntityManagerInterface $em, ?Truc $truc = null): Response
    {
        $truc ??= new Truc();
        $form = $this->createForm(TrucType::class, $truc);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($truc);
            $em->flush();

            return $this->redirectToRoute('truc_show', ['slug' => $truc->slug]);
        }

        return $this->render('truc/edit.html.twig', [
            'form' => $form,
            'truc' => $truc,
        ]);
    }

    #[Route('/trucs/{slug}', name: 'truc_show')]
    public function show(Truc $truc): Response
    {
        return $this->render('truc/show.html.twig', [
            'truc' => $truc,
        ]);
    }


}
