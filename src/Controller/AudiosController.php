<?php

namespace App\Controller;

use App\Entity\Audios;
use App\Entity\AudiosProjet;
use App\Entity\Projet;
use App\Form\MusicIngType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class AudiosController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, private TranslatorInterface $intl)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('{_locale}/client/list/{encodedId}', name: 'list_audios_by_projet')]

    public function listAudiosByProjet(Request $request, string $encodedId, EntityManagerInterface $em): Response
    {

        // Décoder l'encodedId pour obtenir le projetId (qui est un entier)
        $projetId = (int) base64_decode($encodedId);

        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        if (!$user) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les enregistrements AudiosProjet associés au projet donné
        $audiosProjets = $this->entityManager->getRepository(AudiosProjet::class)->findBy(['projet_id' => $projetId]);

        // Récupérer le projet correspondant
        $projet = $this->entityManager->getRepository(Projet::class)->find($projetId);
        if (!$projet) {
            // Gérer le cas où le projet n'est pas trouvé
            throw $this->createNotFoundException('Projet non trouvé');
        }

        // Vérifier si l'utilisateur actuel est autorisé à accéder à ce projet
        if ($projet->getIdClient() !== $user->getUserIdentifier()) {
            // Lancer une exception AccessDeniedException si l'utilisateur n'est pas autorisé
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à ce projet');
        }
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
            $projet_statut = $audioProjet->getFirstAudio();
            // Ajouter l'audio au tableau s'il est trouvé
            if ($audio) {
                $audios[] = [
                    'audio' => $audio,
                    'validerButtonUrl' => $validerButtonUrl,
                    'downloadButtonUrl' => $downloadButtonUrl,
                    'statut' => $projet_statut,
                ];
            }
        }
        $audio = new Audios();
        $formi = $this->createForm(MusicIngType::class, null);
// Gérer la soumission du formulaire
        $formi->handleRequest($request);

        $id_projet = $projetId;

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
                $fileName = 'my_audio' . '.' . $audioId . '.' . $file->guessExtension();

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
                $audiosProjet->setFirstAudio('yes');
                // Enregistrer l'association dans la base de données
                $em->persist($audiosProjet);
                $em->flush();
                $encodedId = base64_encode($id_projet);

            }

            // Rediriger après soumission
            $this->addFlash('success-e', "L'audio a été soumis avec succès.");
            return $this->redirectToRoute('list_audios_by_projet', ['encodedId' => $encodedId]);
        }

        // Renvoyer les audios à la vue pour affichage
        return $this->render('audios/index.html.twig', [
            'audios' => $audios,
            'formi' => $formi->createView(),
        ]);
    }

    #[Route('{_locale}/client/audio/download/{id}', name: 'download_audio')]
    public function download(int $id): BinaryFileResponse
    {
        // Obtenez le chemin absolu vers le dossier public
        $publicPath = $this->getParameter('kernel.project_dir') . '/public';

        // Vérifier le préfixe du nom de fichier
        $audioPrefixes = ['audios_mix', 'my_audio'];
        $filePath = null;

        foreach ($audioPrefixes as $prefix) {
            $potentialFilePath = $publicPath . '/uploads/audio/' . $prefix . '.' . $id . '.mp3';
            if (file_exists($potentialFilePath)) {
                $filePath = $potentialFilePath;
                break;
            }
        }

        // Vérifier si le fichier audio a été trouvé
        if ($filePath !== null) {
            // Créer une réponse pour le fichier audio
            $response = new BinaryFileResponse($filePath);

            // Définir le nom du fichier téléchargé
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'audio_' . $id . '.mp3' // Vous pouvez utiliser le nom de fichier que vous souhaitez
            );

            return $response;
        } else {
            // Si le fichier audio n'existe pas, retourner une réponse 404
            return new Response("Le fichier audio demandé n'existe pas.", Response::HTTP_NOT_FOUND);
        }
    }

}
