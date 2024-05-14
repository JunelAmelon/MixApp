<?php

// src/Controller/CommentaireController.php

namespace App\Controller;

use App\Controller\MailerController;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('{_locale}/client/commentaires/{id_projet}', name: 'liste_commentaires')]
    public function listerCommentaires(MailerController $mailerController,  CommentaireRepository $commentaireRepository, string $id_projet, Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {$id_projet = (int) base64_decode($id_projet);
      $to ='';
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
            $commentaire->setIdAudio($id_projet);
            $commentaire->setIdUser($id_client);
            $commentaire->setRole($role);
            $commentaire->setDate($date);
            $commentaire->setIdProjet($id_projet);
            // Enregistrer le commentaire dans la base de données
            $em->persist($commentaire);
            $em->flush();
            // Appeler la méthode sendEmail avec les paramètres appropriés
$to= $id_client;
            $result = $mailerController->sendEmail('MixApp: Nouveau message', $to, '<p> Vous avez recu un nouveau message de votre Ingenieur son.</p> <br> <p> Veuillez vous connecter pour voir </p>');
$id_projet = base64_encode($id_projet);

// Traiter le résultat si nécessaire
if ($result) {
    // Envoyer un email réussi
    return $this->redirectToRoute('liste_commentaires', ['id_projet' => $id_projet]);

} else {
    // Gérer l'échec de l'envoi de l'email
    return new Response('Échec de l\'envoi de l\'email.', 500);
}

            $this->addFlash('success', 'Votre commentaire a été soumis avec succès');
            
            
            // Rediriger l'utilisateur vers une autre page, par exemple la page de l'audio sur lequel il a commenté
          
            return $this->redirectToRoute('liste_commentaires', ['id_projet' => $id_projet]);
        }
// Récupération de la page courante
        $page = $request->query->getInt('page', 1);

        // Pagination des commentaires
        $commentairesQuery = $commentaireRepository->findCommentairesWithUserIdQuery($id_projet); // Récupérer la requête pour les commentaires
        $pagination = $paginator->paginate(
            $commentairesQuery, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page par défaut
            10// Nombre d'éléments par page
        );
        
        return $this->render('commentaire/commentaires.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'id_projet' => $id_projet,
            'page' => $page,
        ]);}

}
