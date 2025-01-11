<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Repository\PhotoRepository;

class AlbumController extends AbstractController
{
    private AlbumRepository $albumRepository;
    private PhotoRepository $photoRepository;
    private Security $security;

    public function __construct(AlbumRepository $albumRepository, Security $security, PhotoRepository $photoRepository)
    {
        $this->albumRepository = $albumRepository;
        $this->photoRepository = $photoRepository;
        $this->security = $security;
    }

    #[Route('/gallery/album/{id}', name: 'album')]
    #[IsGranted('ROLE_USER')]
    public function getAlbum(int $id): Response
    {
        $album = $this->albumRepository->find($id);

        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        if ($album->getUser()->getId() === $user->getId()) {
            return $this->render('album/index.html.twig', [
                'album' => $album,
            ]);
        } else {
            $this->addFlash('error', "Vous n'êtes pas autorisé à accéder à cet album.");
            return $this->redirectToRoute('gallery');
        }
    }

    #[Route('/gallery/album/photo/{id}', name: 'photo')]
    public function getImage(int $id): Response
    {
        return $this->render('album/modal.html.twig', [
            'photo' => $this->photoRepository->find($id),
        ]);
    }

}
