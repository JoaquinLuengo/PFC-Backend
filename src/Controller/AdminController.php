<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class AdminController extends AbstractController
{

    #[Route('/api')]
    public function api(): Response
    {
        return $this->redirectToRoute('/api');
    }
}