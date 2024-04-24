<?php

// src/Controller/CommentaireController.php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaireController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // #[Route('/commentaire/nouveau/{id_audio}', name: 'commentaire_nouveau')]
    // public function nouveau(Request $request, int $id_audio, EntityManagerInterface $em): Response
    // {
    //     // Créer une nouvelle instance de Commentaire
    //     $commentaire = new Commentaire();

    //     // Créer le formulaire à partir de CommentaireType et associer l'instance de Commentaire
    //     $form = $this->createForm(CommentaireType::class, $commentaire);

    //     // Gérer la soumission du formulaire
    //     $form->handleRequest($request);

    //     // Vérifier si le formulaire a été soumis et est valide
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Récupérer l'utilisateur connecté
    //         $user = $this->getUser();
    //         $id_client = $user->getUserIdentifier();

    //         // Récupérer le rôle de l'utilisateur (client, ingénieur, etc.)
    //         $role = $user->getRoles();

    //         // Récupérer la date actuelle
    //         $date = new \DateTime();

    //         // Remplir les champs manquants du commentaire
    //         $commentaire->setIdAudio($id_audio);
    //         $commentaire->setIdUser($id_client);
    //         $commentaire->setRole($role);
    //         $commentaire->setDate($date);

    //         // Enregistrer le commentaire dans la base de données
    //         $em->persist($commentaire);
    //         $em->flush();

    //         // Rediriger l'utilisateur vers une autre page, par exemple la page de l'audio sur lequel il a commenté
    //         return $this->redirectToRoute('liste_commentaires', ['id_audio' => $id_audio]);
    //     }

    //     // Afficher le formulaire dans le template
    //     return $this->render('commentaire/commentaires.html.twig', [
    //         'form' => $form->createView(),
    //         'commentaires' => $commentaire,

    //     ]);
    // }

#[Route('{_locale}/commentaires/{id_audio}', name: 'liste_commentaires')]
public function listerCommentaires(CommentaireRepository $commentaireRepository, int $id_audio, Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
{
    // Créer une nouvelle instance de Commentaire
    $commentaire = new Commentaire();

    // Créer le formulaire à partir de CommentaireType et associer l'instance de Commentaire
    $form = $this->createForm(CommentaireType::class, $commentaire);

    // Gérer la soumission du formulaire
    $form->handleRequest($request);

    // Vérifier si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        $id_client = $user->getUserIdentifier();

        // Récupérer le rôle de l'utilisateur (client, ingénieur, etc.)
        $role = $user->getRoles();

        // Récupérer la date actuelle
        $date = new \DateTime();

        // Remplir les champs manquants du commentaire
        $commentaire->setIdAudio($id_audio);
        $commentaire->setIdUser($id_client);
        $commentaire->setRole($role);
        $commentaire->setDate($date);

        // Enregistrer le commentaire dans la base de données
        $em->persist($commentaire);
        $em->flush();
        $this->addFlash('success', 'Votre commentaire a été soumis avec succès');
        // Rediriger l'utilisateur vers une autre page, par exemple la page de l'audio sur lequel il a commenté
        return $this->redirectToRoute('liste_commentaires', ['id_audio' => $id_audio]);
    }
// Récupération de la page courante
      $page = $request->query->getInt('page', 1);

    // Pagination des commentaires
    $commentairesQuery = $commentaireRepository->findCommentairesWithUserIdQuery($id_audio); // Récupérer la requête pour les commentaires
    $pagination = $paginator->paginate(
        $commentairesQuery, // Requête à paginer
        $request->query->getInt('page', 1), // Numéro de page par défaut
        10 // Nombre d'éléments par page
    );

    
    return $this->render('commentaire/commentaires.html.twig', [
        'form' => $form->createView(),
        'pagination' => $pagination,
        'id_audio' => $id_audio,
        'page'=> $page,
    ]);

}




}
