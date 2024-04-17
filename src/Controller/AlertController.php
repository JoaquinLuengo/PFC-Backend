<?php

namespace App\Controller;

use App\Entity\Equipo;
use App\Entity\IpAdress;
use App\Entity\SistemaOperativo;
use App\Entity\Switches;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class AlertController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function getSistemasOperativosVigentes():array
    {
        $sistemasOperativosRepository = $this->entityManager->getRepository(SistemaOperativo::class);
        $sistemasOperativos = $sistemasOperativosRepository->findAll();

        $sistemasOperativosArray = [];
        foreach ($sistemasOperativos as $sistemaOperativo) {
            $sistemasOperativosArray[] = $sistemaOperativo->getNombre();
        }

        return $sistemasOperativosArray;
    }

    private function getSwtichsSinConexion():array
    {
        $switchRepository = $this->entityManager->getRepository(Switches::class);
        $ipAdressRepository = $this->entityManager->getRepository(IpAdress::class);
        $switches = $switchRepository->findBy([
            'estadoConexion' => false,
        ]);

        $switchesSinConexion = [];
        foreach ($switches as $switch) {

            $ipAddresses = $ipAdressRepository->findBy(['switches' => $switch]);

            // Inicializa un array para las direcciones IP
            $ipAddressesArray = [];

            // Itera sobre las direcciones IP y obtén el campo "direccion"
            foreach ($ipAddresses as $ipAddress) {
                $ipAddressesArray[] = $ipAddress->getDireccion();
            }

            $switchesSinConexion[] = [
                'ip' => $ipAddressesArray,
                'marca' => $switch->getMarca(),
                'modelo' => $switch->getModelo(),
                'estadoConexion' => $switch->isEstadoConexion(),
                'etiqueta' => $switch->getEtiqueta(),
            ];
        }

        return $switchesSinConexion;
    }

    /**
     * @Route("/alert", name="app_alert_generate")
     */
    public function generateAlerts(): JsonResponse
    {
        //Logica de EQUIPOS Obsoletos
        $repository = $this->entityManager->getRepository(Equipo::class);
        $equipos = $repository->findAll();
        $equiposNoCumplenRestricciones = [];
        $sistemasOperativosVigentes = $this->getSistemasOperativosVigentes();

        foreach ($equipos as $equipo) {
            $memoriaRam = $equipo->getMemoriaRam();

            $sistemaOperativo = $equipo->getSistemaOperativo();
            if ($memoriaRam < 3700 || !in_array($sistemaOperativo, $sistemasOperativosVigentes)) {

                $ip = null;
                if ($equipo->getIpAdress() !== null) {
                    $ip = $equipo->getIpAdress()->getDireccion();
                }

                $equiposNoCumplenRestricciones[] = [
                    'ip' => $ip,
                    'nombre' => $equipo->getNombreDispositivo(),
                    'SO' => $equipo->getSistemaOperativo(),
                    'RAM' => $equipo->getMemoriaRam(),

                ];
            }
        }
        //Logica de Switchs sin conexion
        $switchesSinConexion = $this->getSwtichsSinConexion();
        // Crear un array asociativo con las dos categorías
        $result = [
            'EquiposObsoletos' => $equiposNoCumplenRestricciones,
            'SwitchesSinConexion' => $switchesSinConexion,
        ];
        return new JsonResponse($result);
    }
}
