<?php
// src/Controller/ClientController.php

namespace App\Controller;

use App\Entity\Audios;
use App\Entity\AudiosProjet;
use App\Entity\Projet;
use App\Form\AudioType;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClientController extends AbstractController
{
// private $intl;
    public function __construct(private TranslatorInterface $intl)
    {
// $this->intl = $intl;

    }

    #[Route('/client/{_locale}/creer-projet', name: 'client_creer_projet')]
    public function creerProjet(Request $request, EntityManagerInterface $em): Response
    {
        $client = $this->getUser(); // Assurez-vous que le client est connecté
        $id_client = $client->getUserIdentifier();
        $projet = new Projet();
        $audio = new Audios();
        $form_audio = $this->createForm(AudioType::class, $audio);
        $form_audio->handleRequest($request);

        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projet->setEtatProjet('en attente');
            $projet->setDateCreation(new \DateTime());
            $projet->SetUserIdentifier($id_client);
            // Récupérer le fichier soumis
            $file = $form['files']->getData();
            // Créer une nouvelle instance de Audios avec le nom du fichier temporaire
            $projet->setFiles($file->getClientOriginalName());
            // Persiste l'objet Audios dans la base de données
           

            $em->persist($projet);
            $em->flush(); 
            // Créer une nouvelle instance de Audios avec le nom du fichier temporaire
            $audio->setFiles($file->getClientOriginalName());
            $audio->setDatesAjout(new \DateTime());

            // Mettre à jour l'entité Audios dans la base de données avec le nom final
            $em->persist($audio);
            $em->flush(); // Flush pour obtenir l'ID généré

            // Récupérer l'ID de l'audio après son insertion dans la base de données
            $audioId = $audio->getId();
          
            // Renommer le fichier en utilisant l'ID de l'audio
            $fileName = $audioId . '.' . $file->guessExtension();

            // Déplacer le fichier vers le répertoire souhaité
            $file->move(
                $this->getParameter('audio_directory'), // Configurez ce répertoire dans services.yaml
                $fileName
            );

            // Mettre à jour le nom du fichier dans l'entité Audios avec le nom final
            $audio->setFiles($fileName);
          

            // Mettre à jour l'entité Audios dans la base de données avec le nom final
            $em->persist($audio);
            $em->flush();

            // Créer une nouvelle instance de AudiosProjet et associer les entités manuellement
            $audiosProjet = new AudiosProjet();
            $audiosProjet->setEtatAudio('en cours'); // Remplacez par l'état audio approprié
            //id du projet
            $projetId = $projet->getId();
            // Associe manuellement le projet, l'audio et l'état audio à l'objet AudiosProjet
            $audiosProjet->setProjetId($projetId);
            $audiosProjet->setMyIdAudio($audioId);

            $em->persist($audiosProjet);
            $em->flush();
           

            $this->addFlash('success-p', 'Le projet a été créé avec succès.');

            return $this->redirectToRoute('client_creer_projet');
        }

        return $this->render('client/create-project.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/client/liste-des-projets', name: 'project_list')]
    public function ListerProjet(): Response
    {
        return $this->render('client/listing-projects.html.twig');

    }

// #[Route('/client/{_locale}/soumettre-audio/{projetId}', name: 'client_soumettre_audio')]

// public function soumettreAudio(
//     Request $request,
//     int $projetId,
//     EntityManagerInterface $em,
//     ProjetRepository $projetRepository
// ): Response {
//     $projet = $projetRepository->find($projetId);

//     if (!$projet) {
//         throw $this->createNotFoundException($this->intl->trans("Le projet n'existe pas."));
//     }

//     $audio = new Audios();
//     $form = $this->createForm(AudioType::class, $audio);

//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         // Récupérer le fichier soumis
//         $file = $form['files']->getData();

//         // Créer une nouvelle instance de Audios avec le nom du fichier temporaire
//         $audio->setFiles($file->getClientOriginalName());
//         $audio->setDatesAjout(new \DateTime());

//         // Persiste l'objet Audios dans la base de données
//         $em->persist($audio);
//         $em->flush(); // Flush pour obtenir l'ID généré

//         // Récupérer l'ID de l'audio après son insertion dans la base de données
//         $audioId = $audio->getId();

//         // Renommer le fichier en utilisant l'ID de l'audio
//         $fileName = $audioId . '.' . $file->guessExtension();

//         // Déplacer le fichier vers le répertoire souhaité
//         $file->move(
//             $this->getParameter('audio_directory'), // Configurez ce répertoire dans services.yaml
//             $fileName
//         );

//         // Mettre à jour le nom du fichier dans l'entité Audios avec le nom final
//         $audio->setFiles($fileName);

//         // Mettre à jour l'entité Audios dans la base de données avec le nom final
//         $em->persist($audio);
//         $em->flush();

//         // Créer une nouvelle instance de AudiosProjet et associer les entités manuellement
//         $audiosProjet = new AudiosProjet();
//         $audiosProjet->setEtatAudio('en attente'); // Remplacez par l'état audio approprié
//         // Associe manuellement le projet, l'audio et l'état audio à l'objet AudiosProjet
//         $audiosProjet->setProjetId($projetId);
//         $audiosProjet->setMyIdAudio($audioId);

//         $em->persist($audiosProjet);
//         $em->flush();

//         $this->addFlash('success', $this->intl->trans('Le fichier audio a été soumis avec succès.'));
// /** */
//         return $this->redirectToRoute('app_home');
//     }

//     return $this->render('client/soumettre_audio.html.twig', [
//         'form' => $form->createView(),
//         'projet' => $projet,
//     ]);
// }

    #[Route('/projet/{_locale}/{projetId}/audio/valider/{audioId}', name: 'valider_audio')]
    public function validerAudio(Request $request, int $projetId, int $audioId, EntityManagerInterface $em, ProjetRepository $projetRepository): Response
    {
        $projet = $projetRepository->find($projetId);

        if (!$projet) {
            throw $this->createNotFoundException($this->intl->trans("Le projet n'existe pas."));
        }

        // Vous pouvez ajouter des vérifications supplémentaires ici, par exemple vérifier si l'utilisateur a le droit de valider l'audio

        // Mettre à jour le statut du projet à "valider"
        $projet->setEtatProjet('valider');
        $em->flush();

        // Ajouter un message flash pour confirmer la validation de l'audio
        $this->addFlash('success', $this->intl->trans("L'audio a été validé avec succès."));

        // Rediriger l'utilisateur vers la page de détails du projet ou toute autre page pertinente
        return $this->redirectToRoute('app_home');
    }

    private function disableForeignKeyConstraints(Connection $connection)
    {
        $connection->executeStatement('SET foreign_key_checks = 0;');
    }

    private function enableForeignKeyConstraints(Connection $connection)
    {
        $connection->executeStatement('SET foreign_key_checks = 1;');
    }

}
