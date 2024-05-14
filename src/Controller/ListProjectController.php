<?php

namespace App\Controller;

use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListProjectController extends AbstractController
{
    #[Route('client/{_locale}/list/project', name: 'app_list_project')]
    public function index(ProjetRepository $projetRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les projets associés à cet utilisateur
        // Récupérer le projet de l'utilisateur actuel sans le champ "files"
        $userProject = $projetRepository->findBy([
            'id_client' => $user->getUserIdentifier(),
        ]);

        // Renvoyer la liste des projets à la vue
        $project = $userProject;
        //  dd($project);
        return $this->render('list_project/index.html.twig', [
            'projects' => $project,
        ]);
    }

}
