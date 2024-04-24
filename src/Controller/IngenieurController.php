<?php
// src/Controller/IngenieurController.php

namespace App\Controller;

use App\Entity\Audios;
use App\Entity\Projet;
use App\Entity\Commentaire;
use App\Entity\AudiosProjet;
use App\Form\CommentaireType;
use App\Form\MusicIngType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 
 

class IngenieurController extends AbstractController
{ private $entityManager;

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

#[Route(' /projet/audios/{projetId}', name: 'list_audios_for_project')]
    public function listAudiosByProjet(int $projetId,EntityManagerInterface $entityManager): Response
{
    // Récupérer les enregistrements AudiosProjet associés au projet donné
    $audiosProjets = $this->entityManager->getRepository(AudiosProjet::class)->findBy(['projet_id' => $projetId]);

    // Récupérer le projet correspondant
    $projet = $this->entityManager->getRepository(Projet::class)->find($projetId);

    // Initialiser un tableau pour stocker les audios associés
    $audios = [];

    // Pour chaque enregistrement AudiosProjet, récupérer l'audio correspondant
    foreach ($audiosProjets as $audioProjet) {
        // Récupérer l'audio associé à partir de son identifiant
        $audioId = $audioProjet->getMyIdAudio();
        $audio = $this->entityManager->getRepository(Audios::class)->find($audioId);

        // Vérifier si l'audio est valide avant de rendre les boutons de téléchargement et de validation
        $validerButtonUrl = $projet->isValide() ? null : $this->generateUrl('valider_audio', ['projetId' => $projetId, 'audioId' => $audioId]);
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
 
    // Renvoyer les audios à la vue pour affichage
    return $this->render('ingenieur/list-audios.html.twig', [
        'audios' => $audios,
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
  


#[Route('/ingenieure/commenter/{id_audio}', name: 'lister_commentaires')]
 public function listerCommentaires(CommentaireRepository $commentaireRepository, int $id_audio, Request $request, EntityManagerInterface $em): Response
    {
        $commentaires = $commentaireRepository->findaCommentairesWithUserIdQuery($id_audio);

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $id_ing = 'admin@gmail.com';
            $role = ["ROLE_INGENIEUR"];
            $date = new \DateTime();

            $commentaire->setIdAudio($id_audio);
            $commentaire->setIdUser($id_ing);
            $commentaire->setRole($role);
            $commentaire->setDate($date);

            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a été soumis avec succès');

            return $this->redirectToRoute('lister_commentaires', ['id_audio' => $id_audio]);
        }

        return $this->render('ingenieur/commentaires.html.twig', [
            'form' => $form->createView(),
            'commentaires' => $commentaires,
        ]);
    }
// #[Route('ingenieure/submit-audio/', name: 'envoyer-un-audio')]

#[Route('ingenieure/submit-audio/{projetId?}', name: 'envoyer-audio')]
public function soumettreAudio(Request $request, EntityManagerInterface $em, ?int $projetId= null): Response
{
    // $projetId = $request->attributes->get('projetId');

      
    $audio = new Audios();
    $form = $this->createForm(MusicIngType::class, null);
    // Gérer la soumission du formulaire
    $form->handleRequest($request);
     $projet_Id= $form['projetId']->getData();
    //  dd($projetId);
        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'audio soumis dans la requête
            $file = $form['files']->getData();
        
            // Votre logique de traitement de l'audio (déplacement, enregistrement en base de données, etc.)
            // Créer une nouvelle instance de Audios
          
            $audio->setDatesAjout(new \DateTime());

            // Déplacer le fichier vers le répertoire souhaité
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('audio_directory'),
                $fileName
            );
            $audio->setFiles($fileName);

            // Enregistrer l'audio dans la base de données
            $em->persist($audio);
            $em->flush();

            // Récupérer l'ID de l'audio après son insertion dans la base de données
            $audioId = $audio->getId();
           
            // Récupérer l'ID du projet associé à l'audio depuis la requête
            // Créer une nouvelle instance de AudiosProjet et associer les entités manuellement
            $audiosProjet = new AudiosProjet();
            $audiosProjet->setEtatAudio('en cours');
            $audiosProjet->setProjetId($projet_Id);
            $audiosProjet->setMyIdAudio($audioId);

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
 

// #[Route('/ingenieur/dashboard', name: 'ingenieur_dashborad')] 
// public function afficherpage(): Response
// { 
    
//     return $this->render('ingenieur/soumettre-audio.html.twig', [
//            ]);
// }

}
