<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListProjectController extends AbstractController
{
    #[Route('client/list/project', name: 'app_list_project')]
    public function index(): Response
    {
        return $this->render('list_project/index.html.twig', [
            'controller_name' => 'ListProjectController',
        ]);
    }
}
