<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Entity\Switches;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\Agente;
use App\Entity\IpAdress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AgenteController extends AbstractController
{
    /**
     * @Route("/eliminar-agente/{id}", name="eliminar_agente", methods={"POST"})
     */
    public function eliminarAgente(int $id, EntityManagerInterface $entityManager)
    {
        $agente = $entityManager->getRepository(Agente::class)->find($id);

        if (!$agente) {
            return new JsonResponse(['message' => 'Agente no encontrado'], 404);
        }

        $switches = $entityManager->getRepository(Switches::class)->findBy(['agente' => $agente] );

        //Un agente puede tener varios switch a cargo
        if($switches){
            foreach ($switches  as $switch ) {
                $switch->setAgente(null);
                $entityManager->persist($switch);
            }

        }
        // Remuevo la relacion que hay entre Ipadress -> Agente
        //$ipAdress = $agente->getIpadress();
        $ipAdress = $entityManager->getRepository(IpAdress::class)->findBy(['agente' => $agente] );
        if($ipAdress){
            foreach ($switches  as $ip ) {
                $ip->setAgente(null);
                $entityManager->persist($ip);
            }
        }

        // Lógica para eliminar un Agente
        $entityManager->remove($agente);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Agente eliminado correctamente']);
    }

    /**
     * @Route("/actualizar-agente/{id}", name="actualizar_agente", methods={"POST"})
     */
    public function actualizarAgente(Request $request, int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $agente = $entityManager->getRepository(Agente::class)->find($id);

        if (!$agente) {
            return new JsonResponse(['message' => 'Agente no encontrado'], 404);
        }

        $jsonData = $request->getContent();
        // Deserializa el JSON en un objeto Agente
        $nuevoAgente = $serializer->deserialize($jsonData, Agente::class, 'json');

        // Valida el objeto Agente
        $errors = $validator->validate($nuevoAgente);

        if (count($errors) > 0) {
            // Maneja los errores de validación
            return new JsonResponse(['errors' => $errors], 400);
        }

        // Actualiza los campos del Agente existente con los nuevos valores
        $agente->setNombre($nuevoAgente->getNombre());
        $agente->setApellido($nuevoAgente->getApellido());
        $agente->setSector($nuevoAgente->getSector());


        // Si se proporciona información de IpAdress, actualiza la dirección IP
        $ipAdressData = json_decode($jsonData, true);
        if (isset($ipAdressData['ipAdress'])) {
            //Chequear octeto [que sea octeto la ip nueva]
            $patron = '/^(?:\d{1,3}\.){3}\d{1,3}$/';
            if( preg_match($patron, $ipAdressData['ipAdress']['direccion'] ) != 1 ) { return new JsonResponse(['message' =>  'Error en el patron '.$ipAdressData['ipAdress']['direccion'] ]);}

            // Verificar si ya existe una dirección IP en la base de datos
            $existingIp = $entityManager->getRepository(IpAdress::class)->findOneBy([
                'direccion' => $ipAdressData['ipAdress']['direccion']
            ]);
            // return new JsonResponse(['message' =>  $ipAdressData['ipAdress']['direccion'] ]);
            if ($existingIp) {
                // Verifica si la dirección IP ya está asociada a otro Agente [en ese registro]
                $existingAgenteInRegIpadd = $existingIp->getAgente(); //Obtengo el Agente de la IP a actualizar
                if ($existingAgenteInRegIpadd ) {
                    // Si ya está asociada a otro Agente, desasigna la dirección IP
                    $existingAgenteInRegIpadd ->setIpAdress(null);
                    //ademas desde ipadress tengo que nulearlo tambien
                    $existingIp->setAgente(null);
                    $entityManager->persist($existingAgenteInRegIpadd);
                    $entityManager->persist($existingIp);
                }
                //Verifico si el Agente esta asociodo en otra direccion IP
                $findNowAgenteInOtherIpadd = $entityManager->getRepository(IpAdress::class)->findOneBy([
                    'agente' => $agente->getId()
                ]);

                if ($findNowAgenteInOtherIpadd) {
                    // Si ya está asociada a otro Agente, desasigna la dirección IP
                    //ademas desde ipadress tengo que nulearlo tambien
                    $findNowAgenteInOtherIpadd->setAgente(null);
                    $entityManager->persist($findNowAgenteInOtherIpadd);
                }
                // Asigna la dirección IP al nuevo Agente
                $existingIp->setAgente($agente);
                //la relacion devuelta
                $agente->setIpadress($existingIp);
                $entityManager->persist($existingIp);
                $entityManager->persist($agente);
            } else {
                return new JsonResponse(['message' =>  'La direccion IP No Existe: '.$ipAdressData['ipAdress']['direccion'] ]);
            }
        }else{
            // Si "ipAdress" es null, desasigna la dirección IP existente
            if ($agente->getIpadress()) {
                $existingIp = $agente->getIpadress();
                $existingIp->setAgente(null);
                $entityManager->persist($existingIp);
                $agente->setIpadress(null);
            }
        }

                $entityManager->flush();

               return new JsonResponse(['message' => 'Agente actualizado correctamente']);


    }
    /**
     * @Route("/agregar-agente", name="agregar_agente", methods={"POST"})
     */
    public function agregarAgente(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $jsonData = $request->getContent();


        $agente = $serializer->deserialize($jsonData, Agente::class, 'json');
        $ipAdressData = json_decode($jsonData, true);

        $ipAdressData = json_decode($jsonData, true);
        if (isset($ipAdressData['ipAdress'])) {
            $patron = '/^(?:\d{1,3}\.){3}\d{1,3}$/';
            if( preg_match($patron, $ipAdressData['ipAdress']['direccion'] ) != 1 ) { return new JsonResponse(['message' =>  'Error en el patron '.$ipAdressData['ipAdress']['direccion'] ]);}

            $existingIp = $entityManager->getRepository(IpAdress::class)->findOneBy([
                'direccion' => $ipAdressData['ipAdress']['direccion']
            ]);
            if ($existingIp) {
                $existingAgenteInRegIpadd = $existingIp->getAgente();
                if ($existingAgenteInRegIpadd ) {
                    $existingAgenteInRegIpadd ->setIpAdress(null);
                    $existingIp->setAgente(null);
                    $entityManager->persist($existingAgenteInRegIpadd);
                    $entityManager->persist($existingIp);
                }

                $existingIp->setAgente($agente);

                $agente->setIpadress($existingIp);
                $entityManager->persist($existingIp);
                $entityManager->persist($agente);
            } else {
                return new JsonResponse(['message' =>  'La direccion IP No Existe, agregala: '.$ipAdressData['ipAdress']['direccion'] ]);
            }
        }

        // Valida el objeto Agente
        $errors = $validator->validate($agente);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $errors], 400);
        }

        $entityManager->persist($agente);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Agente agregado correctamente']);
    }
}
