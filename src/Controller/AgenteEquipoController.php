<?php

namespace App\Controller;

use App\Entity\IpAdress;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AgenteEquipoController extends AbstractController{

  private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}
public function getRelacion()
{
    $em = $this->entityManager;
    $qb = $em->createQueryBuilder();

    try {
        // Obtengo aquellos agentes y equipos que tengan la misma ip
        $relacion = $qb->select('ip.direccion, agente.nombre as nombre_agente, agente.apellido as apellido_agente, equipo.nombreDispositivo as nombre_dispositivo, equipo.macaddress as mac_dispositivo')
            ->from(IpAdress::class, 'ip')
            ->leftJoin('ip.agente', 'agente')
            ->leftJoin('ip.equipo', 'equipo')
            ->where('ip.agente IS NOT NULL')
            ->andWhere('ip.equipo IS NOT NULL')
            ->getQuery()
            ->getResult();

        return [
             $relacion,
        ];
    } catch (\Exception $e) {
        return [
            'error' => 'Ocurrió un error al obteener relacion entre agentes y equipos.',
        ];
    }
}
    #[Route('/agente-equipo', name: 'app_agente_equipo')]
    public function getAgenteEquipo(): JsonResponse
    {
        $relacion = $this->getRelacion();
    //return new JsonResponse($data, 200);
    return new JsonResponse(['Relacion' => $relacion ], 200);
    }
}
