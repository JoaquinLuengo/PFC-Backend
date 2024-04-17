<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Switches;
use App\Entity\IpAdress;
use App\Entity\Sector;


class SwitchesController extends AbstractController
{
    /**
     * @Route("/eliminar-switch/{id}", name="eliminar_switch", methods={"POST"})
     */
    public function eliminarSwitch(int $id, EntityManagerInterface $entityManager)
    {
        $switch = $entityManager->getRepository(Switches::class)->find($id);

        if (!$switch) {
            return new JsonResponse(['message' => 'Switch no encontrado'], 404);
        }

        // Obtén la relación con la IpAdress y establece la relación en null
        $ipAdress = $switch->getIpadress();
        if ($ipAdress) {
            $ipAdress->setSwitches(null);
            $entityManager->persist($ipAdress);
        }
        // Lógica para eliminar un Switch
        $entityManager->remove($switch);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Switch eliminado correctamente']);
    }

    /**
     * @Route("/actualizar-switch/{id}", name="actualizar_switch", methods={"POST"})
     */
    public function actualizarSwitch(Request $request, int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $switch = $entityManager->getRepository(Switches::class)->find($id);

        if (!$switch) {
            return new JsonResponse(['message' => 'Switch no encontrado'], 404);
        }

        $jsonData = $request->getContent();
        $nuevoSwitch = $serializer->deserialize($jsonData, Switches::class, 'json');
        $errors = $validator->validate($nuevoSwitch);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $errors], 400);
        }

        $switch->setMarca($nuevoSwitch->getMarca());
        $switch->setModelo($nuevoSwitch->getModelo());
        $switch->setEstadoConexion($nuevoSwitch->isEstadoConexion());
        $switch->setSector($nuevoSwitch->getSector());
        $switch->setEtiqueta($nuevoSwitch->getEtiqueta());
        $switch->setAgente($nuevoSwitch->getAgente());

        $ipAdressData = json_decode($jsonData, true);

        if (isset($ipAdressData['ipAdress'])) {
            $patron = '/^(?:\d{1,3}\.){3}\d{1,3}$/';
            if( preg_match($patron, $ipAdressData['ipAdress']['direccion'] ) != 1 ) { return new JsonResponse(['message' =>  'Error en el patron '.$ipAdressData['ipAdress']['direccion'] ]);}

            $existingIp = $entityManager->getRepository(IpAdress::class)->findOneBy([
                'direccion' => $ipAdressData['ipAdress']['direccion']
            ]);

            if ($existingIp) {
                $existingSwitchInRegIpadd = $existingIp->getSwitches();
                if ($existingSwitchInRegIpadd) {
                    $existingSwitchInRegIpadd->setIpAdress(null);
                    $existingIp->setSwitches(null);
                    $entityManager->persist($existingSwitchInRegIpadd);
                    $entityManager->persist($existingIp);
                }

                $findNowSwitchInOtherIpadd = $entityManager->getRepository(IpAdress::class)->findOneBy([
                    'switches' => $switch->getId()
                ]);

                if ($findNowSwitchInOtherIpadd) {
                    //ademas desde ipadress tengo que nulearlo tambien
                    $findNowSwitchInOtherIpadd->setSwitches(null);
                    $entityManager->persist($findNowSwitchInOtherIpadd);
                }
                    // Asigna la dirección IP al nuevo Switch
                    $existingIp->setSwitches($switch);
                    //la relacion devuelta
                    $switch->setIpadress($existingIp);
                    $entityManager->persist($existingIp);
                    $entityManager->persist($switch);
            } else {
                return new JsonResponse(['message' =>  'La direccion IP No Existe, agregala: '.$ipAdressData['ipAdress']['direccion'] ]);

            }
        }else{
            // Si "ipAdress" es null, desasigna la dirección IP existente
            if ($switch->getIpadress()) {
                $existingIp = $switch->getIpadress();
                $existingIp->setSwitches(null);
                $entityManager->persist($existingIp);
                $switch->setIpadress(null);
            }
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Switch actualizado correctamente']);
    }

    /**
     * @Route("/agregar-switch", name="agregar_switch", methods={"POST"})
     */
    public function agregarSwitch(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $jsonData = $request->getContent();
        $switch = $serializer->deserialize($jsonData, Switches::class, 'json');
        $ipAdressData = json_decode($jsonData, true);

        if (isset($ipAdressData['ipAdress'])) {
            $patron = '/^(?:\d{1,3}\.){3}\d{1,3}$/';
            if( preg_match($patron, $ipAdressData['ipAdress']['direccion'] ) != 1 ) { return new JsonResponse(['message' =>  'Error en el patron '.$ipAdressData['ipAdress']['direccion'] ]);}

            $existingIp = $entityManager->getRepository(IpAdress::class)->findOneBy([
                'direccion' => $ipAdressData['ipAdress']['direccion']
            ]);

            if ($existingIp) {

                // Verifica si la dirección IP ya está asociada a otro Switch [en ese registro]
                $existingSwitchInRegIpadd = $existingIp->getSwitches(); //Obtengo el switch de la IP a actualizar
                if ($existingSwitchInRegIpadd) {
                    // Si ya está asociada a otro Switch, desasigna la dirección IP
                    $existingSwitchInRegIpadd->setIpAdress(null);
                    //ademas desde ipadress tengo que nulearlo tambien
                    $existingIp->setSwitches(null);
                    $entityManager->persist($existingSwitchInRegIpadd);
                    $entityManager->persist($existingIp);
                }

                // Asigna la dirección IP al nuevo Switch
                $existingIp->setSwitches($switch);
                //la relacion devuelta
                $switch->setIpadress($existingIp);
                $entityManager->persist($existingIp);
                $entityManager->persist($switch);
            } else {
                return new JsonResponse(['message' =>  'La direccion IP No Existe, agregala: '.$ipAdressData['ipAdress']['direccion'] ]);
            }
        }

        $errors = $validator->validate($switch);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $errors], 400);
        }

        $entityManager->persist($switch);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Switch agregado correctamente']);
    }
}

