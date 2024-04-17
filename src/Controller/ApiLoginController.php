<?php

namespace App\Controller;

use phpDocumentor\Reflection\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\ApiToken;
use Doctrine\ORM\EntityManagerInterface;



class ApiLoginController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    #[Route('/login', name: 'app_login')]
    public function index(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Crea un nuevo ApiToken para el usuario actual
       // $apiToken = new ApiToken();
       // $apiToken->setOwnedBy($user);

        // Persiste el ApiToken en la base de datos
        //$this->entityManager->persist($apiToken);
       // $this->entityManager->flush();
        return $this->json([
              'user'  => $user->getUserIdentifier(),
              'token' => $user->getApiTokensArray(),
            // 'token' => $apiToken->getToken(), // Devuelve el token recién creado
          ]);
    }
    #[Route('/logout', name:'app_logout')]
    public function logout(): JsonResponse
    {

        // Verificar si el usuario está autenticado en Symfony 6
        if (!$this->getUser()) {
            return new JsonResponse(['message' => 'No hay usuario autenticado.'], 403);
        }

        // Aquí, debes eliminar el token asociado al usuario actual
        $user = $this->getUser(); // Obtiene el usuario actual
        if ($user) {
            $apiTokenRepository = $this->entityManager->getRepository(ApiToken::class);
            $apiToken = $apiTokenRepository->findOneBy(['ownedBy' => $user]);

            if ($apiToken) {
                $this->entityManager->remove($apiToken);
                $this->entityManager->flush();
            }
        }

        return new JsonResponse(['message' => $user->getUserIdentifier()], 200);
    }
}
