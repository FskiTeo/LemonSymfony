<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LieuController extends AbstractController
{
    #[Route('/lieux', name: 'app_lieu')]
    #[IsGranted('ROLE_ADMIN', statusCode: 404)]
    public function index(LieuRepository $lieuRepository, ?string $alertMessage): Response
    {
        $params = ['lieux' => $lieuRepository->findAll(), 'alertMessage' => ""];
        if(isset($alertMessage) && !empty($alertMessage)){
            $params['alertMessage'] = $alertMessage;
        }

        return $this->render('lieu/index.html.twig', $params);
    }

    #[Route("/supprimerlieu/{id}", name: "app_delete_lieu")]
    #[IsGranted('ROLE_ADMIN', statusCode: 404)]
    public function deleteLieu(int $id, EntityManagerInterface $manager, LieuRepository $lieuRepository): Response
    {
        try {
            $lieu = $lieuRepository->find($id);
            $manager->remove($lieu);
            $manager->flush();
            return $this->redirectToRoute('app_lieu');
        } catch (Exception $e) {
            return $this->redirectToRoute('app_lieu', ['alertMessage' => "Impossible de supprimer ce lieu car il est utilisé par un ou plusieurs événements."]);
        }

    }
}
