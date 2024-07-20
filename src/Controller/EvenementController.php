<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\CreateEvenementType;
use App\Repository\EvenementRepository;
use App\Repository\LieuRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EvenementController extends AbstractController
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/create', name: 'app_create_evenement_form')]
    #[IsGranted('ROLE_USER', statusCode: 404)]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security, UtilisateurRepository $utilisateurRepository): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(CreateEvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $securityUser = $security->getUser()->getUserIdentifier();
            $user = $utilisateurRepository->findOneBy(["email" => $securityUser]);
            $evenement->setCreateur($user);

            $evenement->addInscrit($user);

            $entityManager->persist($evenement);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('evenement/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/inscription/{id}', name: 'app_inscription_evenement')]
    #[IsGranted('ROLE_USER', statusCode: 404)]
    public function inscription(int $id, UtilisateurRepository $utilisateurRepository, EvenementRepository $evenementRepository, EntityManagerInterface $manager): Response
    {
        $securityUser = $this->security->getUser();
        if ($securityUser) {
            $utilisateur = $utilisateurRepository->findOneBy(["email" => $securityUser->getUserIdentifier()]);
            if ($utilisateur) {
                $evenement = $evenementRepository->find($id);
                if ($evenement) {
                    $evenement->addInscrit($utilisateur);
                    $manager->persist($evenement);
                    $manager->flush();
                }
            }
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/desinscription/{id}', name: 'app_desinscription_evenement')]
    #[IsGranted('ROLE_USER', statusCode: 404)]
    public function desinscription(int $id, UtilisateurRepository $utilisateurRepository, EvenementRepository $evenementRepository, EntityManagerInterface $manager): Response
    {
        $securityUser = $this->security->getUser();
        if ($securityUser) {
            $utilisateur = $utilisateurRepository->findOneBy(["email" => $securityUser->getUserIdentifier()]);
            if ($utilisateur) {
                $evenement = $evenementRepository->find($id);
                if ($evenement) {
                    $utilisateur->removeInscription($evenement);
                    $manager->persist($utilisateur);
                    $manager->persist($evenement);
                    $manager->flush();
                }
            }
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/edit/{id}', name: 'app_edit_evenement_form')]
    #[IsGranted('ROLE_USER', statusCode: 404)]
    public function editEvent(int $id, Request $request, EntityManagerInterface $manager, EvenementRepository $evenementRepository, UtilisateurRepository $utilisateurRepository): Response
    {
        $evenement = $evenementRepository->find($id);
        $securityUser = $this->security->getUser();
        if ($evenement && $securityUser) {
            $utilisateur = $utilisateurRepository->findOneBy(["email" => $securityUser->getUserIdentifier()]);
            if ($evenement->getCreateur() == $utilisateur || in_array("ROLE_ADMIN", $utilisateur->getRoles())){
                $form = $this->createForm(CreateEvenementType::class, $evenement);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $manager->persist($evenement);
                    $manager->flush();
                    return $this->redirectToRoute('app_home');
                }

                return $this->render('evenement/edit.html.twig', [
                    'form' => $form,
                ]);
            }
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/delete/{id}', name: 'app_delete_evenement')]
    public function delete(int $id, UtilisateurRepository $utilisateurRepository, EvenementRepository $evenementRepository, EntityManagerInterface $manager): Response
    {
        $evenement = $evenementRepository->find($id);
        $utilisateur = $utilisateurRepository->findOneBy(["email" => $this->security->getUser()->getUserIdentifier()]);

        if ($evenement) {
            if(in_array("ROLE_ADMIN", $utilisateur->getRoles()) || $evenement->getCreateur() == $utilisateurRepository->findOneBy(["email" => $this->security->getUser()->getUserIdentifier()])) {
                $manager->remove($evenement);
                $manager->flush();
            }
        }

        return $this->redirectToRoute('app_home');
    }
}
