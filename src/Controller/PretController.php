<?php

namespace App\Controller;

use App\Entity\Pret;
use App\Entity\Truc;
use App\Form\PretType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PretController extends AbstractController
{

    #[Route('/trucs/{slug}/preter', name: 'truc_preter')]
    public function preter(Truc $truc, Request $request, EntityManagerInterface $em): Response
    {
        if ($truc->isPrete()) {
            $this->addFlash('danger', 'Ce truc est déjà prêté');
            return $this->redirectToRoute('truc_show', ['slug' => $truc->slug]);
        }

        $pret = new Pret($truc);
        $form = $this->createForm(PretType::class, $pret);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($pret);
            $em->flush();

            return $this->redirectToRoute('truc_show', ['slug' => $truc->slug]);
        }

        return $this->render('truc/pret.html.twig', [
            'form' => $form,
            'truc' => $truc,
        ]);
    }

    #[Route('/trucs/{slug}/rendre', name: 'truc_rendre')]
    public function rendre(Truc $truc, EntityManagerInterface $em): Response
    {
        if (!$truc->isPrete()) {
            $this->addFlash('danger', 'Ce truc n\'est pas prêté');
            return $this->redirectToRoute('truc_show', ['slug' => $truc->slug]);
        }

        /** @var Pret $pret */
        $pret = $truc->prets->first();
        $pret->dateFin = new \DateTime();
        $em->flush();

        $this->addFlash('success', 'Rendu !');
        return $this->redirectToRoute('truc_show', ['slug' => $truc->slug]);
    }



}
