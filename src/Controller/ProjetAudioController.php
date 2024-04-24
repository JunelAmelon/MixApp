<?php

namespace App\Controller;
use App\Entity\Audios;

use App\Entity\Projet;
use App\Entity\AudiosProjet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class ProjetAudioController extends AbstractController
{
    public function __construct(private TranslatorInterface $intl)
    {
       
    }
    #[Route('/projet/{_locale}/{id}/audios', name: 'app_projet_audios')]
    public function listerAudios(EntityManagerInterface $em, int $id): Response
    {
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();
    // Vérifier si l'utilisateur est connecté
    if (!$user) {
        // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
        return $this->redirectToRoute('app_login');
    }

    // Récupérer le projet associé à cet utilisateur et à l'ID spécifié
    $projet = $em->getRepository(Projet::class)->findOneBy(['id' => $id, 'id_client' => $user-> getUserIdentifier()]);
    // Vérifier si le projet existe et appartient à l'utilisateur connecté
    if (!$projet) {
        // Rediriger l'utilisateur vers une page d'erreur ou une autre page appropriée
        throw $this->createNotFoundException($this->intl->trans("Projet non trouvé ou n'appartient pas à l'utilisateur connecté."));
    }

    // Récupérer les identifiants d'audio associés à ce projet depuis la table AudiosProjet
    $audiosProjetRepository = $em->getRepository(AudiosProjet::class);
    $audioIds = $audiosProjetRepository->findBy(['projet_id' => $projet->getId()], ['id' => 'ASC']);

    // Initialiser un tableau pour stocker les fichiers audio
    $audioFiles = [];
    // Parcourir les identifiants d'audio pour récupérer les fichiers associés
    foreach ($audioIds as $audioId) {
        // Récupérer l'audio correspondant à cet identifiant
        $audio = $em->getRepository(Audios::class)->find($audioId->getMyIdAudio());
        // Ajouter le fichier audio au tableau
        if ($audio) {
            $audioFiles[] = $audio->getFiles();
        }
    }

    // Renvoyer la liste des fichiers audio à la vue
    return $this->render('projet_audios/index.html.twig', [
        'projet' => $projet,
        'audioFiles' => $audioFiles,
    ]);
    }
    

}
