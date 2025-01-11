<?php

namespace App\Controller;

use App\Form\UploadType;
use App\Entity\User;
use App\Message\UploadMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Repository\AlbumRepository;

class GalleryController extends AbstractController
{
    private $messageBus;
    private $albumRepository;
    private $galleryDirectory;

    public function __construct(MessageBusInterface $messageBus, AlbumRepository $albumRepository, $galleryDirectory)
    {
        $this->messageBus = $messageBus;
        $this->albumRepository = $albumRepository;
        $this->galleryDirectory = $galleryDirectory;
    }

    #[Route('/gallery', name: 'gallery')]
    #[IsGranted('ROLE_USER')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('gallery/index.html.twig', [
            'albums' => $this->albumRepository->findBy(['user' => $user->getId()])
        ]);
    }

    #[Route('/gallery/upload', name: 'gallery_upload')]
    #[IsGranted('ROLE_USER')]
    public function upload(Request $request): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filePaths = [];
            foreach ($form->get('image')->getData() as $file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $filePath = $this->galleryDirectory . $fileName;

                // Déplacer le fichier uploadé vers le répertoire permanent
                $file->move($this->galleryDirectory, $fileName);
                $filePaths[] = $filePath;
            }

            $albumId = $form->get('album_name')->getData() ? $form->get('album_name')->getData()->getId() : null;
            $dateTaken = $form->get('date_taken')->getData();
            $newAlbumName = $form->get('new_album_name')->getData();
            $userId = $this->getUser()->getId();

            $this->messageBus->dispatch(new UploadMessage($albumId, $filePaths, $dateTaken, $newAlbumName, $userId));

            $this->addFlash('success', 'Fichier(s) uploadé(s) avec succès. Le traitement se fera en arrière-plan.');

            return $this->redirectToRoute('gallery');
        }

        return $this->render('upload/index.html.twig', [
            'form' => $form,
        ]);
    }
}
