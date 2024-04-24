<?php

namespace App\Controller;

use App\Entity\Audios;
use App\Entity\Projet;
use App\Entity\AudiosProjet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AudiosController extends AbstractController
{
 
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, private TranslatorInterface $intl)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('{_locale}/list/{projetId}', name: 'list_audios_by_projet')]
    // public function listAudiosByProjet(int $projetId): Response
    // {
    //     // Récupérer les enregistrements AudiosProjet associés au projet donné
    //     $audiosProjets = $this->entityManager->getRepository(AudiosProjet::class)->findBy(['projet_id' => $projetId]);

    //     // Initialiser un tableau pour stocker les audios associés
    //     $audios = [];

    //     // Pour chaque enregistrement AudiosProjet, récupérer l'audio correspondant
    //     foreach ($audiosProjets as $audioProjet) {
    //         // Récupérer l'audio associé à partir de son identifiant
    //         $audioId = $audioProjet->getMyIdAudio();
    //         $audio = $this->entityManager->getRepository(Audios::class)->find($audioId);

    //         // Ajouter l'audio au tableau s'il est trouvé
    //         if ($audio) {
    //             $audios[] = $audio;
    //         }
    //     }

    //     // Renvoyer les audios à la vue pour affichage
    //     return $this->render('audios/index.html.twig', [
    //         'audios' => $audios,
    //         'projetId' => $projetId, // Passer l'ID du projet à la vue
    //     ]);
    // }
public function listAudiosByProjet(int $projetId): Response
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
    return $this->render('audios/index.html.twig', [
        'audios' => $audios,
    ]);
}


    #[Route('{_locale}/audio/download/{id}', name: 'download_audio')]
    public function download(int $id): BinaryFileResponse
    {
        // Obtenez le chemin absolu vers le dossier public
        $publicPath = $this->getParameter('kernel.project_dir') . '/public';

        // Construire le chemin vers le fichier audio
        $filePath = $publicPath . '/uploads/audio/' . $id . '.mp3';

        // Vérifier si le fichier audio existe
        if (file_exists($filePath)) {
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
            return new Response($this->intl->trans("Le fichier audio demandé n'existe pas."), Response::HTTP_NOT_FOUND);
        }

    }

}
