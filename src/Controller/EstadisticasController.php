<?php

namespace App\Controller;

use App\Entity\Agente;
use App\Entity\Equipo;
use App\Entity\IpAdress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EstadisticasController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
   public function getSistemasOperativos(){

       $em = $this->entityManager;
       $qb = $em->createQueryBuilder();

       $query = $this->entityManager->createQueryBuilder()
           ->select('e.sistemaOperativo, COUNT(e.id) AS cantidad')
           ->from(Equipo::class, 'e')
           ->groupBy('e.sistemaOperativo')
           ->getQuery()
           ->getResult();

       $totalEquipos = 0;
       $porcentajes = [];

       foreach ($query as $row) {
           $totalEquipos += $row['cantidad'];
       }

       foreach ($query as $row) {
           $porcentaje = ($row['cantidad'] / $totalEquipos) * 100;
           $porcentajes[$row['sistemaOperativo']] = $porcentaje;
       }

       return $porcentajes;
   }
    public function getMemoriasRam(){
        $em = $this->entityManager;
        $qb = $em->createQueryBuilder();

        $ramGroups = $qb->select('CASE 
                        WHEN e.memoriaRam < 3700 THEN \'Menor_a_3700\'
                        WHEN e.memoriaRam >= 3700 AND e.memoriaRam <= 7800 THEN \'Entre_3700_y_7800\'
                        ELSE \'Mayor_a_7800\'
                        END AS ram_group, COUNT(e.id) AS cantidad')
            ->from(Equipo::class, 'e')
            ->groupBy('ram_group')
            ->getQuery()
            ->getResult();

        $totalEquipos = 0;
        $porcentajesMemoriaRAM = [];

        foreach ($ramGroups as $row) {
            $totalEquipos += $row['cantidad'];
        }

        if ($totalEquipos > 0) {
            foreach ($ramGroups as $row) {
                $porcentaje = ($row['cantidad'] / $totalEquipos) * 100;
                $porcentajesMemoriaRAM[$row['ram_group']] = $porcentaje;
            }
        }

        return $porcentajesMemoriaRAM;
    }


    //Agentes sin equipo
    public function getPorcentajeAgentesSinEquipo()
    {
        $em = $this->entityManager;
        $qb = $em->createQueryBuilder();

        try {
            // Contar agentes con equipo asignado
             $agentesConEquipo  = $qb->select('COUNT(ip.id) AS cantidad1')
                ->from(IpAdress::class, 'ip')
                ->where('ip.agente IS NOT NULL')
                ->andWhere('ip.equipo IS NOT NULL')
                ->getQuery()->getSingleScalarResult();

            // Contar agentes sin equipo asignado
            $agentesSinEquipo = $qb->select('COUNT(ip2.id) AS cantidad2')
                ->from(IpAdress::class, 'ip2')
                ->where('ip2.agente IS NOT NULL')
                ->andWhere('ip2.equipo IS  NULL')
                ->getQuery()->getSingleScalarResult();

            $totalAgentes = $agentesConEquipo + $agentesSinEquipo;

            $porcentajeAgentesConEquipo = ($agentesConEquipo / $totalAgentes) * 100;
            $porcentajeAgentesSinEquipo = ($agentesSinEquipo / $totalAgentes) * 100;

            return [
                'con_equipo' => $porcentajeAgentesConEquipo,
                'sin_equipo' => $porcentajeAgentesSinEquipo,
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Ocurrió un error al calcular los porcentajes.',
            ];
        }
    }

    public function getPorcentajeDuplicadas()
    {
        $em = $this->entityManager;
        $qb = $em->createQueryBuilder();


        try {
            // Contar cantidad de ips con SWITCH Y AGENTE iguales y que no tenga switch
            $caso1 = $qb->select('COUNT(ip.id) AS cantidad1')
                ->from(IpAdress::class, 'ip')
                ->where('ip.agente IS NOT NULL')
                ->andWhere('ip.switches IS NOT NULL')
                ->andWhere('ip.equipo IS NULL')
                ->getQuery()->getSingleScalarResult();

            //cantidad de equipos y switch sin agentes - REPETIDA EN 2
            $caso2 = $this->entityManager->createQueryBuilder()
                ->select('COUNT(ip2.id) AS cantidad2')
                ->from(IpAdress::class, 'ip2')
                ->where('ip2.equipo IS NOT NULL')
                ->andWhere('ip2.switches IS NOT NULL')
                ->andWhere('ip2.agente IS NULL')
                ->getQuery()
                ->getSingleScalarResult();

            //REPETIDA EN LOS 3
            $caso3 = $this->entityManager->createQueryBuilder()
                ->select('COUNT(ip4.id) AS cantidad4')
                ->from(IpAdress::class, 'ip4')
                ->where('ip4.equipo IS NOT NULL')
                ->andWhere('ip4.switches IS NOT NULL')
                ->andWhere('ip4.agente IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult();

            //Cantidad total de ips
            $total = $this->entityManager->createQueryBuilder()
                ->select('COUNT(ip3.direccion) AS cantidad3')
                ->from(IpAdress::class, 'ip3')
                ->getQuery()
                ->getSingleScalarResult();

            $porcentajeIpDuplicadas = (($caso1 + $caso2 + $caso3) / $total) * 100;

            return [
                'porcentaje' => $porcentajeIpDuplicadas,
                'caso1' => $caso1,
                'caso2' => $caso2,
                'caso3' => $caso3,
                'total' => $total,
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Ocurrió un error al calcular los porcentajes.',
            ];
        }
    }
    #[Route('/estadisticas', name: 'app_estadisticas')]
    public function estadisticas(): JsonResponse
    {
        $porcentajesSistemasOperativos = $this->getSistemasOperativos();
        $porcentajesMemoriaRAM = $this->getMemoriasRam();
        $porcentajesAgentes = $this->getPorcentajeAgentesSinEquipo();
        $porcentajesIpDuplicadas = $this->getPorcentajeDuplicadas();
        $data = [
            'sistemas_operativos' => $porcentajesSistemasOperativos,
            'memorias_ram' =>    $porcentajesMemoriaRAM ,
            'agentes' => $porcentajesAgentes,
            'ip_duplicadas' => $porcentajesIpDuplicadas

        ];

        return new JsonResponse($data, 200);
    }
}
