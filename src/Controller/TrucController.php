<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Truc;
use App\Entity\User;
use App\Form\TrucType;
use App\Search\TagSearch;
use App\Search\TrucSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrucController extends AbstractController
{
    #[Route('/trucs', name: 'trucs_list')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $search = new TrucSearch([
            ...$request->query->all(),
            'publie' => true,
        ]);

        if ($request->query->getBoolean("me")) {
            $search->user = $this->getUser();
            $search->publie = null;
        }

        $trucs = $em->getRepository(Truc::class)->search($search);

        return $this->render('truc/index.html.twig', [
            'trucs' => $trucs,
        ]);
    }

    #[Route('/nouveau-truc', name: 'trucs_add')]
    #[Route('/trucs/{slug}/modifier', name: 'truc_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        ?Truc $truc = null,
    ): Response
    {
        $truc ??= new Truc();
        $form = $this->createForm(TrucType::class, $truc);

        $tags = $em->getRepository(Tag::class)->search(new TagSearch(['limit' => 0]));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($truc);
            $em->flush();

            return $this->redirectToRoute('truc_show', ['slug' => $truc->slug]);
        }

        return $this->render('truc/edit.html.twig', [
            'form' => $form,
            'truc' => $truc,
            'tags' => $tags,
        ]);
    }

    #[Route('/trucs/{slug}/uploads', name: 'truc_add_image', methods: ['POST'])]
    public function trucAddImage(Request $request, EntityManagerInterface $em, Truc $truc): Response
    {
        $slugger = new AsciiSlugger();

        // Handle files
        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        $file->move($this->getParameter('upload_dir'), $newFilename);
        $image = new Image();
        $image->filename = $newFilename;
        $truc->addImage($image);
        $em->persist($image);
        $em->flush();

        return $this->json([
            'success' => true,
            'image' => $image,
        ]);
    }

    #[Route('/trucs/{slug}/uploads/{id}', name: 'truc_del_image', methods: ['DELETE'])]
    public function trucDelImage(
        EntityManagerInterface $em,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Truc $truc,
        #[MapEntity(mapping: ['id' => 'id'])]
        Image $image
    ): Response
    {
        $em->remove($image);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('trucs/{slug}/publier', name: 'truc_publier', methods: ['POST'])]
    public function publier(Request $request, EntityManagerInterface $em, Truc $truc): Response
    {
        $truc->publie = $request->request->getBoolean('publie');
        $em->flush();

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    #[Route('/trucs/{slug}/delete', name: 'truc_delete')]
    public function delete(Truc $truc, EntityManagerInterface $em): Response
    {
        $em->remove($truc);
        $em->flush();
        return $this->redirectToRoute('trucs_list');
    }

    #[Route('/trucs/{slug}', name: 'truc_show')]
    public function show(Truc $truc): Response
    {
        return $this->render('truc/show.html.twig', [
            'truc' => $truc,
        ]);
    }




}
