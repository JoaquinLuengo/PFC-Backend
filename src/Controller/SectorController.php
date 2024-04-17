<?php

namespace App\Controller;

use App\Entity\Agente;
use App\Entity\Sector;
use App\Entity\Switches;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SectorController extends AbstractController
{
    /**
     * @Route("/eliminar-sector/{id}", name="eliminar_sector", methods={"DELETE"})
     */
    public function eliminarSector(int $id, EntityManagerInterface $entityManager)
    {
        $sector = $entityManager->getRepository(Sector::class)->find($id);

        if (!$sector) {
            return new JsonResponse(['message' => 'Sector no encontrada'], 404);
        }
        $switches = $entityManager->getRepository(Switches::class)->findBy(['sector' => $sector] );

        //Un agente puede tener varios switch a cargo
        if($switches){
            foreach ($switches  as $switch ) {
                $switch->setSector(null);
                $entityManager->persist($switch);
            }

        }

        $agentes = $entityManager->getRepository(Agente::class)->findBy(['sector' => $sector] );

        //Un agente puede tener varios switch a cargo
        if($agentes){
            foreach ($agentes  as $agente ) {
                $agente->setSector(null);
                $entityManager->persist($agente);
            }
        }
        // Lógica para eliminar un Agente
        $entityManager->remove($sector);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Sector eliminado correctamente']);
    }
}
