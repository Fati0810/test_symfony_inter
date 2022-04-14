<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaFormType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class AdminMediaController extends AbstractController
{
    /**
     * @Route("admin/medias", name="admin_media_list")
     */
    public function adminListMedia(MediaRepository $mediaRepository)
    {
        $images = $mediaRepository->findAll();

        return $this->render("admin/media_list.html.twig", ['images' => $images]);
    }

    /**
     * @Route("admin/media/{id}", name="admin_media_show")
     */
    public function adminShowMedia(MediaRepository $mediaRepository, $id)
    {
        $image = $mediaRepository->find($id);

        return $this->render("admin/media_show.html.twig", ['image' => $image]);

    }
    /**
     * @Route("admin/create/media", name="admin_create_media")
    */
    public function adminCreateMedia(request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $media = New Media();

        $mediaForm = $this->createForm(MediaFormType::class, $media);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()){
            
            $mediaFile = $mediaForm->get('src')->getData();

            if($mediaFile){
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = $sluggerInterface->slug($originalFilename);
                
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();
                
                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $media->setSrc($newFilename);
            }
            $entityManagerInterface->persist($media);
            $entityManagerInterface->flush();
            return $this->redirectToRoute("admin_product_list");
        }
        
        return $this->render("admin/media_form.html.twig", ['mediaForm' => $mediaForm->createView()]);


    }


    /**
     * @Route("/admin/update/media/{id}", name="admin_media_update")
     */
    public function adminMediaUpdate(
        $id,
        MediaRepository $mediaRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request, 
        SluggerInterface $sluggerInterface
    ) {

        $media = $mediaRepository->find($id);

        $mediaForm = $this->createForm(MediaFormType::class, $media);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            $mediaFile = $mediaForm->get('src')->getData();

            if ($mediaFile) {
                $originalFielname = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $sluggerInterface->slug($originalFielname);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();
                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $media->setSrc($newFilename);

        }

        $entityManagerInterface->persist($media);

        $entityManagerInterface->flush();

        return $this->redirectToRoute("admin_product_list");

    }

    return $this->render("admin/media_form.html.twig", ['mediaForm' => $mediaForm->createView()]);

  }

    /**
     * @Route("/delete/media/{id}", name="media_delete")
     */
    public function adminDeleteMedia(
        $id,
        EntityManagerInterface $entityManagerInterface,
        MediaRepository $mediaRepository
    ) {
        $media = $mediaRepository->find($id);

        $entityManagerInterface->remove($media);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('admin_media_list');
    }

}
