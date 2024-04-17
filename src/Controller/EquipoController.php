<?php

namespace App\Controller;

use App\Entity\SistemaOperativo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\Equipo;
use App\Entity\IpAdress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
class EquipoController extends AbstractController
{
    private $entityM;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityM = $entityManager;
    }
    /**
     * @Route("/obtener-datos-de-api", name="obtener_datos_de_api")
     */
    public function obtenerDatosDeApi(EntityManagerInterface $entityManager): Response
    {
        // Crea una instancia de HttpClient
        $client = HttpClient::create();
        // Inicializa un arreglo para almacenar las direcciones IP
        $direccionesIP = [];

        for ($i = 1; $i <= 4000; $i++) {
            // Define la URL del endpoint para el equipo actual
            $url = 'http://10.7.6.7/ocsapi/v1/computer/' . $i . '/hardware';

            // Realiza la solicitud GET
            $response = $client->request('GET', $url);

            // Verifica si la solicitud fue exitosa (código de respuesta 200)
            if ($response->getStatusCode() === 200) {
                // Convierte la respuesta JSON en un array asociativo
                $content = $response->getContent();
                if($content != "null"){
                    $data = $response->toArray();
                    // Accede a la dirección IP (IPADDR)
                    if (isset($data[$i]['hardware']['IPADDR'])) {
                        $direccionIP = $data[$i]['hardware']['IPADDR'];
                        $direccionesIP[] = $direccionIP;

                        // Busca si ya existe una entidad IPAddress con esta dirección IP
                        $ipAddressExistente = $entityManager->getRepository(IpAdress::class)
                            ->findOneBy(['direccion' => $direccionIP]);

                        if($ipAddressExistente){
                            // Obtiene el equipo relacionado con esta dirección IP
                            $equipo = $ipAddressExistente->getEquipo();
                        }
                        // Verifica si la entidad IPAdress existe
                        else{
                            // Si no existe la dirección IP, crea una nueva entidad IPAdress
                            $ipAddressExistente = new IpAdress();
                            $ipAddressExistente->setDireccion($direccionIP);
                            // Crea un nuevo equipo y asigna la dirección IP
                            $equipo = new Equipo();
                        }
                        // Actualiza los campos del equipo
                        $equipo->setNombreDispositivo($data[$i]['hardware']['NAME']);
                        $equipo->setSistemaOperativo($data[$i]['hardware']['OSNAME']);
                        $equipo->setMemoriaRam($data[$i]['hardware']['MEMORY']);

                        // Establece la relación entre IPAdress y Equipo
                        $ipAddressExistente->setEquipo($equipo);

                        // Guarda las entidades en la base de datos
                        $entityManager->persist($equipo);
                        $entityManager->persist($ipAddressExistente);
                        $entityManager->flush();
                    }
                }
            } else {

            }
        }

        return $this->json(['direccionesIP' => $direccionesIP]);
    }

    private function getSistemasOperativosVigentes():array
    {
        $sistemasOperativosRepository = $this->entityM->getRepository(SistemaOperativo::class);
        $sistemasOperativos = $sistemasOperativosRepository->findAll();

        $sistemasOperativosArray = [];
        foreach ($sistemasOperativos as $sistemaOperativo) {
            $sistemasOperativosArray[] = $sistemaOperativo->getNombre();
        }

        return $sistemasOperativosArray;
    }
    /**
     * @Route("/obtener-equipos", name="obtener_equipos", methods={"GET"})
     */
    public function obtenerEquipos(): JsonResponse
    {
        //Logica de EQUIPOS Obsoletos
        $equipos =  $repository = $this->entityM->getRepository(Equipo::class)->findAll();
        $allEquipos = [];
        $sistemasOperativosVigentes = $this->getSistemasOperativosVigentes();

        foreach ($equipos as $equipo) {
            $memoriaRam = $equipo->getMemoriaRam();

            $rambool = true;
            $sobool = true;
            $sistemaOperativo = $equipo->getSistemaOperativo();
            if ($memoriaRam < 3700 ){
                $rambool = false;
            }
            if(!in_array($sistemaOperativo, $sistemasOperativosVigentes)) {
                $sobool = false;
            }
            $ip = null;
            if ($equipo->getIpAdress() !== null) {
                $ip = $equipo->getIpAdress()->getDireccion();
            }
            $allEquipos[] = [
                'nombreDispositivo' => $equipo->getNombreDispositivo(),
                'sistemaOperativo' => $equipo->getSistemaOperativo(),
                'memoriaRam' => $equipo->getMemoriaRam(),
                'macaddress' => $equipo->getMacaddress(),
                'sogood' => $sobool,
                'ramgood' => $rambool,
                'ip' => $ip,
            ];
        }

       /* // Crear un array asociativo con las dos categorías
        $result = [
            'EquiposObsoletos' => $equiposNoCumplenRestricciones,
            'SwitchesSinConexion' => $switchesSinConexion,
        ];*/
        return new JsonResponse($allEquipos);
    }
}
