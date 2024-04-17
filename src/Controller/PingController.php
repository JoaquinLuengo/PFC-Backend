<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;

class PingController extends AbstractController
{
    private $ipAddresses = [
        '10.7.7.3',
        '10.7.7.5','10.7.7.6','10.7.7.7','10.7.7.8','10.7.7.9','10.7.7.5','10.7.7.5',

    ];
    /**
     * @Route("/ping", name="ping_all")
     */
    public function pingAction(): JsonResponse
    {
        // Direcciones IP
        $ipAddresses = [
            '192.162.0.1',
            '192.162.1.1',
        ];

        $pingResults = [];

        foreach ($ipAddresses as $ipAddress) {
            // Crea un nuevo objeto Process para ejecutar el comando ping
            $process = new Process(['ping', '-c', '4', $ipAddress]); // Ejecuta 4 pings

            // Ejecuta el proceso y obtén la salida
            $process->run();

            // Obtiene la salida del proceso
            $output = $process->getOutput();

            // Almacena los resultados en un arreglo asociativo
            $pingResults[$ipAddress] = $output;
        }

        // Devuelve los resultados en formato JSON
        return $this->json($pingResults);
    }
}
