<?php

namespace App\Controller;

use App\Entity\IpAdress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IpConflictivasController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/ip-conflictivas", name="app_alert_generate")
     */
    public function ipConflictivas(): JsonResponse
    {
        try {
            $em = $this->entityManager;
            $qb = $em->createQueryBuilder();
            $query = $this->entityManager->createQueryBuilder()
                ->select('ip')
                ->from(IpAdress::class, 'ip')
                ->where($qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('ip.agente'),
                        $qb->expr()->isNotNull('ip.switches')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('ip.switches'),
                        $qb->expr()->isNotNull('ip.equipo')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('ip.agente'),
                        $qb->expr()->isNotNull('ip.switches'),
                        $qb->expr()->isNotNull('ip.equipo')
                    )
                ))
                ->getQuery()
                ->getResult();

            // QueryBuilder para "Agentes sin equipos asignados"
            $qb2 = $em->createQueryBuilder();

            $query2 = $this->entityManager->createQueryBuilder()
                ->select('ip')
                ->from(IpAdress::class, 'ip')
                ->where($qb2->expr()->andX(
                    $qb2->expr()->isNotNull('ip.agente'),
                    $qb2->expr()->isNull('ip.equipo')
                ))
                ->getQuery()
                ->getResult();
            /*
            if (empty($query)) {
                return new JsonResponse(['message' => 'No se encontraron resultados para "Agentes sin equipos asignados".']);
            }

            if (empty($query2)) {
                return new JsonResponse(['message' => 'No se encontraron resultados para "Agentes sin equipos asignados".']);
            }
            */
            // Transforma los resultados en un array de datos
            $data['ipsDuplicadas'] = [];
            foreach ($query as $ipAdress) {
                $ipData = [
                    'id' => $ipAdress->getId(),
                    'direccion' => $ipAdress->getDireccion(),
                ];

                if ($ipAdress->getAgente() !== null) {
                    $ipData['agente'] = [
                        'nombre' => $ipAdress->getAgente()->getNombre(),
                        'apellido' => $ipAdress->getAgente()->getApellido(),
                    ];
                }

                if ($ipAdress->getSwitches() !== null) {
                    $ipData['switch'] = [
                        'etiqueta' => $ipAdress->getSwitches()->getEtiqueta(),
                    ];
                }

                if ($ipAdress->getEquipo() !== null) {
                    $ipData['equipo'] = [
                        'nombreDispositivo' => $ipAdress->getEquipo()->getNombreDispositivo(),
                    ];
                }

                $data['ipsDuplicadas'][] = $ipData;
            }

            $data['agenteSinEquipo'] = [];
            foreach ($query2 as $ipAdress) {
                $ipData = [
                    'id' => $ipAdress->getId(),
                    'direccion' => $ipAdress->getDireccion(),
                ];

                if ($ipAdress->getAgente() !== null) {
                    $ipData['agente'] = [
                        'nombre' => $ipAdress->getAgente()->getNombre(),
                        'apellido' => $ipAdress->getAgente()->getApellido(),
                    ];
                }
                if ($ipAdress->getEquipo() !== null) {
                    $ipData['equipo'] = [
                        'nombreDispositivo' => $ipAdress->getEquipo()->getNombreDispositivo(),
                    ];
                }

                $data['agenteSinEquipo'][] = $ipData;
            }

            return new JsonResponse($data, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
