<?php

namespace App\Controller;

use App\Entity\IpAdress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IpadressController extends AbstractController
{
    //No se creo el /actualizar porque suponemos que esa "direccion" va a seguir asociada a lo que estaba
    /**
     * @Route("/eliminar-ipadress/{id}", name="eliminar_ipadress", methods={"DELETE"})
     */
    public function eliminarIpAdress(int $id, EntityManagerInterface $entityManager)
    {
        $ip = $entityManager->getRepository(IpAdress::class)->find($id);

        if (!$ip) {
            return new JsonResponse(['message' => 'Direccion IP no encontrada'], 404);
        }

        // Remuevo la relacion que hay dentro de IP con agente
        $agente = $ip->getAgente();
        if ($agente) {
            $agente->setIpadress(null);
            $ip->setAgente(null);
            $entityManager->persist($agente);
            $entityManager->persist($ip);
        }
        // Remuevo la relacion que hay dentro de IP con agente
        $switch = $ip->getSwitches();
        if ($switch) {
            $switch->setIpadress(null);
            $ip->setSwitches(null);
            $entityManager->persist($switch);
            $entityManager->persist($ip);
        }
        //Si tengo una relacion con equipo, que hago elimino o alerto? -> en el proximo CRON lo agrega
        $equipo = $ip->getEquipo();
        if ($equipo) {
            $equipo->setIpadress(null);
            $ip->setEquipo(null);
            $entityManager->persist($equipo);
            $entityManager->persist($ip);
        }

        // Lógica para eliminar un Agente
        $entityManager->remove($ip);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Direccion IP eliminado correctamente']);
    }
}
