<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_home')]
    public function index(EvenementRepository $evenementRepository, UtilisateurRepository $utilisateurRepository): Response
    {
        $securityUser = $this->security->getUser();
        if($securityUser){
            $utilisateur = $utilisateurRepository->findOneBy(["email"=>$securityUser->getUserIdentifier()]);
            $inscriptions = $utilisateur->getInscriptions();
        }

        $evenements = $evenementRepository->findAllOrderByDate();

        $evenements_to_display = [];

        foreach ($evenements as $evenement) {
            $evenement_to_add = [
                "id" => $evenement->getId(),
                "titre" => $evenement->getTitre(),
                "description" => $evenement->getDescription(),
                "debut" => $evenement->getDebut(),
                "fin" => $evenement->getFin(),
                "lieu" => $evenement->getLieu()->getLabel(),
                "createur" => $evenement->getCreateur()->getFullName(),
                "inscrits" => $evenement->getInscrits()->count(),
                "isCreateur" => false
            ];

            if($securityUser){

                if(in_array($evenement, $inscriptions->toArray())){
                    $evenement_to_add["isInscrit"] = true;
                } else {
                    $evenement_to_add["isInscrit"] = false;
                }

                if($evenement->getCreateur() == $utilisateur){
                    $evenement_to_add["isCreateur"] = true;
                }
            }
            $evenements_to_display[] = $evenement_to_add;
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'evenements' => $evenements_to_display,
        ]);
    }


    #[Route('/mes-inscriptions', name: 'app_mes_inscriptions')]
    #[IsGranted('ROLE_USER', statusCode: 404)]
    public function mesInscriptions(UtilisateurRepository $utilisateurRepository): Response
    {
        $securityUser = $this->security->getUser();
        if(!$securityUser){
            return $this->redirectToRoute('app_login');
        }

        $utilisateur = $utilisateurRepository->findOneBy(["email"=>$securityUser->getUserIdentifier()]);
        $inscriptions = $utilisateur->getInscriptions();

        $evenements_to_display = [];

        foreach ($inscriptions as $evenement) {
            $evenement_to_add = [
                "id" => $evenement->getId(),
                "titre" => $evenement->getTitre(),
                "description" => $evenement->getDescription(),
                "debut" => $evenement->getDebut(),
                "fin" => $evenement->getFin(),
                "createur" => $evenement->getCreateur()->getFullName(),
                "inscrits" => $evenement->getInscrits()->count()
            ];

            $evenements_to_display[] = $evenement_to_add;
        }

        return $this->render('home/mes_inscriptions.html.twig', [
            'evenements' => $evenements_to_display,
        ]);
    }
}
