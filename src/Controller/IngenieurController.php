<?php
// src/Controller/IngenieurController.php

namespace App\Controller;

use App\Controller\MailerController;
use App\Entity\Audios;
use App\Entity\AudiosProjet;
use App\Entity\Commentaire;
use App\Entity\Projet;
use App\Entity\User;
use App\Form\CommentaireType;
use App\Form\MusicIngType;
use App\Form\UserType;
use App\Repository\CommentaireRepository;
use App\Repository\ProjetRepository;
use App\Repository\UserRepository;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class IngenieurController extends AbstractController
{private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, private TranslatorInterface $intl)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/ingenieur/projets', name: 'ingenieur_projets')]
    public function listerProjets(EntityManagerInterface $em): Response
    {
        // Récupérer tous les projets avec les informations associées
        $projets = $em->getRepository(Projet::class)->findAll();

        // Afficher les projets dans un template
        return $this->render('ingenieur/projets.html.twig', [
            'projets' => $projets,
        ]);
    }

    #[Route(' ingenieur/projet/audios/{id_projet}', name: 'list_audios_for_project')]
    public function listAudiosByProjet(ProjetRepository $projetRepository, int $id_projet, MailerController $mailerController, EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository, Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer les enregistrements AudiosProjet associés au projet donné
        $audiosProjets = $this->entityManager->getRepository(AudiosProjet::class)->findBy(['projet_id' => $id_projet]);

        // Récupérer le projet correspondant
        $projet = $this->entityManager->getRepository(Projet::class)->find($id_projet);
        $to = '';

        // Initialiser un tableau pour stocker les audios associés
        $audios = [];

        // Pour chaque enregistrement AudiosProjet, récupérer l'audio correspondant
        foreach ($audiosProjets as $audioProjet) {
            // Récupérer l'audio associé à partir de son identifiant
            $audioId = $audioProjet->getMyIdAudio();
            $audio = $this->entityManager->getRepository(Audios::class)->find($audioId);

            // Vérifier si l'audio est valide avant de rendre les boutons de téléchargement et de validation
            $validerButtonUrl = $projet->isValide() ? null : $this->generateUrl('valider_audio', ['projetId' => $id_projet, 'audioId' => $audioId]);
            $downloadButtonUrl = $projet->isValide() ? $this->generateUrl('download_audio', ['id' => $audioId]) : null;

            // Ajouter l'audio au tableau s'il est trouvé
            if ($audio) {
                $audios[] = [
                    'audio' => $audio,
                    'validerButtonUrl' => $validerButtonUrl,
                    'downloadButtonUrl' => $downloadButtonUrl,
                ];
            }
        }

        $commentaires = $commentaireRepository->findaCommentairesWithUserIdQuery($id_projet);
        // Récupérer le projet par son id_projet
        $projet = $this->entityManager->getRepository(Projet::class)->findOneBy([
            'id' => $id_projet,
        ]);

        if (!$projet) {
            throw new \Exception('Projet non trouvé pour cet identifiant.');
        }

// Récupérer l'id_client du projet
        $idClient = $projet->getIdClient(); // Supposons que getIdClient() retourne l'id_client
        $to = $idClient;

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $id_ing = 'admin@gmail.com';
            $role = ["ROLE_INGENIEUR"];
            $date = new \DateTime();

            $commentaire->setIdAudio($id_projet);
            $commentaire->setIdUser($id_ing);
            $commentaire->setRole($role);
            $commentaire->setDate($date);
            $commentaire->setIdProjet($id_projet);

            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a été soumis avec succès');
            // Créer une instance de MailerController

// Appeler la méthode sendEmail avec les paramètres appropriés
            $result = $mailerController->sendEmail('MixApp: Nouveau message', $to, '<p> Vous avez recu un nouveau message de votre Ingenieur son.</p> <br> <p> Veuillez vous connecter pour voir </p>');

// Traiter le résultat si nécessaire
            if ($result) {
                // Envoyer un email réussi
                return $this->redirectToRoute('list_audios_for_project', ['id_projet' => $id_projet]);

            } else {
                // Gérer l'échec de l'envoi de l'email
                return new Response('Échec de l\'envoi de l\'email.', 500);
            }

        }

        $audio = new Audios();
        $formi = $this->createForm(MusicIngType::class, null);
// Gérer la soumission du formulaire
        $formi->handleRequest($request);
        $projet_Id = $formi['projetId']->getData();
//dd($projetId);
// Vérifier si le formulaire a été soumis et est valide
        if ($formi->isSubmitted()) {
            // Récupérer l'audio soumis dans la requête
            $file = $formi['files']->getData();
            foreach ($file as $file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Votre logique de traitement de l'audio (déplacement, enregistrement en base de données, etc.)
                // Créer une nouvelle instance de Audios
                $audio->setFiles($fileName);

                $audio->setDatesAjout(new \DateTime());
// Mettre à jour l'entité Audios dans la base de données avec le nom final
                $em->persist($audio);
                $em->flush(); // Flush pour obtenir l'ID généré

// Récupérer l'ID de l'audio après son insertion dans la base de données
                $audioId = $audio->getId();

                // Déplacer le fichier vers le répertoire souhaité
                $fileName = 'audios_mix' . '.' . $audioId . '.' . $file->guessExtension();

                $file->move(
                    $this->getParameter('audio_directory'),
                    $fileName
                );
                $audio->setFiles($fileName);

                // Enregistrer l'audio dans la base de données
                $em->persist($audio);
                $em->flush();

                // Récupérer l'ID du projet associé à l'audio depuis la requête
                // Créer une nouvelle instance de AudiosProjet et associer les entités manuellement
                $audiosProjet = new AudiosProjet();
                $audiosProjet->setEtatAudio('en cours');
                $audiosProjet->setProjetId($id_projet);
                $audiosProjet->setMyIdAudio($audioId);
                $audiosProjet->setFirstAudio('no');
                // Enregistrer l'association dans la base de données
                $em->persist($audiosProjet);
                $em->flush();
            }
            // Rediriger après soumission
            $this->addFlash('success-e', "L'audio a été soumis avec succès.");
            return $this->redirectToRoute('list_audios_for_project', ['id_projet' => $id_projet]);
        }

        // Renvoyer les audios à la vue pour affichage
        return $this->render('ingenieur/list-audios.html.twig', [
            'audios' => $audios,
            'form' => $form->createView(),
            'commentaires' => $commentaires,
            'formi' => $formi->createView(),
        ]);
    }

    #[Route('/ingenieur/dashboard', name: 'ingenieur_dashborad')]

    public function dashboard(ProjetRepository $projetRepository): Response
    {
        // Récupération de tous les projets
        $projets = $projetRepository->findAll();

        // Nombre total des projets
        $nombreTotalProjets = count($projets);

        // Requête pour obtenir la somme totale des projets ayant le statut "valider"
        $nombreProjetsValides = $projetRepository->countByNombreProjetsValides('valider');

        return $this->render('ingenieur/index.html.twig', [
            'projets' => $projets,
            'nombreTotalProjets' => $nombreTotalProjets,
            'nombreProjetsValides' => $nombreProjetsValides,
        ]);
    }

    #[Route('/ingenieur/commenter/{id_projet}', name: 'lister_commentaires')]
    public function listerCommentaires(CommentaireRepository $commentaireRepository, int $id_projet, Request $request, EntityManagerInterface $em): Response
    {
        $commentaires = $commentaireRepository->findaCommentairesWithUserIdQuery($id_projet);

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $id_ing = 'admin@gmail.com';
            $role = ["ROLE_INGENIEUR"];
            $date = new \DateTime();

            $commentaire->setIdAudio($id_projet);
            $commentaire->setIdUser($id_ing);
            $commentaire->setRole($role);
            $commentaire->setDate($date);
            $commentaire->setIdProjet($id_projet);

            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a été soumis avec succès');

            return $this->redirectToRoute('lister_commentaires', ['id_projet' => $id_projet]);
        }

        return $this->render('ingenieur/commentaires.html.twig', [
            'form' => $form->createView(),
            'commentaires' => $commentaires,
        ]);
    }
// #[Route('ingenieure/submit-audio/', name: 'envoyer-un-audio')]

    #[Route('ingenieur/submit-audio/{projetId?}', name: 'envoyer-audio')]
    public function soumettreAudio(Request $request, EntityManagerInterface $em, ?int $projetId = null): Response
    {
        // $projetId = $request->attributes->get('projetId');

        $audio = new Audios();
        $form = $this->createForm(MusicIngType::class, null);
        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        $projet_Id = $form['projetId']->getData();
        //dd($projetId);
        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'audio soumis dans la requête
            $file = $form['files']->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Votre logique de traitement de l'audio (déplacement, enregistrement en base de données, etc.)
            // Créer une nouvelle instance de Audios
            $audio->setFiles($fileName);

            $audio->setDatesAjout(new \DateTime());
// Mettre à jour l'entité Audios dans la base de données avec le nom final
            $em->persist($audio);
            $em->flush(); // Flush pour obtenir l'ID généré

// Récupérer l'ID de l'audio après son insertion dans la base de données
            $audioId = $audio->getId();

            // Déplacer le fichier vers le répertoire souhaité
            $fileName = $audioId . '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('audio_directory'),
                $fileName
            );
            $audio->setFiles($fileName);

            // Enregistrer l'audio dans la base de données
            $em->persist($audio);
            $em->flush();

            // Récupérer l'ID du projet associé à l'audio depuis la requête
            // Créer une nouvelle instance de AudiosProjet et associer les entités manuellement
            $audiosProjet = new AudiosProjet();
            $audiosProjet->setEtatAudio('en cours');
            $audiosProjet->setProjetId($projet_Id);
            $audiosProjet->setMyIdAudio($audioId);
            $audiosProjet->setFirstAudio('no');
            // Enregistrer l'association dans la base de données
            $em->persist($audiosProjet);
            $em->flush();

            // Rediriger après soumission
            $this->addFlash('success-e', "L'audio a été soumis avec succès.");
            return $this->redirectToRoute('envoyer-audio');
        }

        // Afficher le formulaire
        return $this->render('ingenieur/soumettre-audio.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/ingenieur/create-user', name: 'create_user')]
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, SecurityAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données soumises par le formulaire
            $userData = $form->getData();

            // Récupérer les rôles sélectionnés (array) depuis le formulaire
            $selectedRoles = $userData->getRoles();

            // Assurez-vous que $selectedRoles est bien un tableau (array)
            if (is_array($selectedRoles)) {
                // Convertir le tableau de rôles en une chaîne de caractères (séparés par des virgules par exemple)
                $rolesAsString = implode(',', $selectedRoles);

                // Affecter la chaîne de caractères de rôles à l'entité User
                $user->setRoles([$rolesAsString]);
            }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // Enregistrer l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection ou autre traitement après la création de l'utilisateur
            $this->addFlash('success', 'Utilisateur créé avec succès.');
            return $this->redirectToRoute('create_user');
        }

        return $this->render('ingenieur/create-user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ingenieur/delete/{id}', name: 'user_delete')]
    public function deleteUser(int $id, Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        // Rechercher l'utilisateur par son ID
        $user = $userRepository->find($id);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé avec l\'ID : ' . $id);
        }

        // Supprimer l'utilisateur de la base de données
        $entityManager->remove($user);
        $entityManager->flush();

        // Redirection vers une autre page ou affichage d'un message de confirmation
        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('user_list'); // Rediriger vers la liste des utilisateurs
    }

    #[Route('/ingenieur/list/users', name: 'user_list')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('ingenieur/list-user.html.twig', [
            'users' => $users,
        ]);
    }}
