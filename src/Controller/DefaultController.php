<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('trucs_list');
        }
        return $this->render('home.html.twig');
    }

}
