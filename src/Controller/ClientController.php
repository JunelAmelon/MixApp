<?php
// src/Controller/ClientController.php

namespace App\Controller;

use App\Entity\Audios;
use App\Entity\AudiosProjet;
use App\Entity\Projet;
use App\Form\AudioType;
use App\Form\ProjetType;
use App\Repository\AudiosRepository;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client/creer-projet', name: 'client_creer_projet')]
    public function creerProjet(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($projet);
            $entityManager->flush();

            $this->addFlash('success', 'Le projet a été créé avec succès.');

            return $this->redirectToRoute('client_soumettre_audio', ['projetId' => $projet->getId()]);
        }

        return $this->render('client/creer_projet.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/client/soumettre-audio/{projetId}', name: 'client_soumettre_audio')]
public function soumettreAudio(
    Request $request,
    int $projetId,
    EntityManagerInterface $entityManager,
    ProjetRepository $projetRepository
): Response {
    $projet = $projetRepository->find($projetId);

    if (!$projet) {
        throw $this->createNotFoundException('Le projet n\'existe pas.');
    }

    $audio = new Audios();
    $form = $this->createForm(AudioType::class, $audio);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le fichier soumis
        $file = $form['files']->getData();

        // Renommer le fichier si nécessaire pour éviter les collisions
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        // Déplacer le fichier vers le répertoire souhaité
        $file->move(
            $this->getParameter('audio_directory'), // Configurez ce répertoire dans services.yaml
            $fileName
        );

        // Créer une nouvelle instance de Audios
        $audio->setFiles($fileName);
        $audio->setDatesAjout(new \DateTime());

        // Persiste l'objet Audios dans la base de données
        $entityManager->persist($audio);
        $entityManager->flush(); // Flush pour obtenir l'ID généré

        // Créer une nouvelle instance de AudiosProjet et associer les entités manuellement
        $audiosProjet = new AudiosProjet();
        $audiosProjet->setEtatAudio('en attente'); // Remplacez par l'état audio approprié

        // Associe manuellement le projet, l'audio et l'état audio à l'objet AudiosProjet
        $audiosProjet->setIdProjet($projet->getId());
        $audiosProjet->setIdAudio($audio->getId()); // Utilisez l'ID de l'audio nouvellement persisté
        $entityManager->persist($audiosProjet);
        dd($projet->getId(), $audiosProjet->getIdProjet(), $audiosProjet->getId());
        // Applique les changements dans la base de données
        $entityManager->flush();

        $this->addFlash('success', 'Le fichier audio a été soumis avec succès.');

        return $this->redirectToRoute('app_home');
    }

    return $this->render('client/soumettre_audio.html.twig', [
        'form' => $form->createView(),
        'projet' => $projet,
    ]);
}



}
