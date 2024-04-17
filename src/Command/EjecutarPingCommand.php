<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\IpAdress;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:ejecutar-ping',
    description: 'Ejecuta un comando PING externo y procesa los resultados.',
)]
class EjecutarPingCommand extends Command
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }
    protected function configure(): void
    {
        $this->setDescription('Ejecuta un comando PING externo y procesa los resultados.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // Inicializar un arreglo para almacenar los resultados
        $pingedResults = [];
        // Obtener todas las direcciones IP que tienen un equipo relacionado
        // Crear una consulta personalizada para obtener direcciones IP asociadas a equipos
        $query = $this->entityManager->createQueryBuilder()
            ->select('ip')
            ->from(IpAdress::class, 'ip')
            ->where('ip.switches IS NOT NULL')
            ->getQuery();

        // Obtener las direcciones IP utilizando la consulta personalizada
        $ipAddresses = $query->getResult();

        foreach ($ipAddresses as $ipAddress) {
            $ip = $ipAddress->getDireccion();

            // Ejecutar el comando PING y obtener la salida y el código de retorno
            exec('ping -c 4 ' . escapeshellarg($ip), $output, $returnCode);

            // Verificar el código de retorno
            $pingSuccess = ($returnCode === 0); // El PING es exitoso si el código de retorno es 0

            $switches = $ipAddress->getSwitches();
            if ($switches) {
                // Si se encontró una entidad Switches, establecer el valor de estadoConexion
                $switches->setEstadoConexion($pingSuccess);
            }
            // Agregar el resultado al arreglo, solo si el PING tuvo éxito
            $pingedResults[] = [
                'ip' => $ip,
                 'ping_success' => $pingSuccess,
                   // 'ping_result' => implode("\n", $output), // La salida del PING
                ];


        }
        // Salida de los resultados como JSON
        echo json_encode($pingedResults, JSON_PRETTY_PRINT) . PHP_EOL; //Comentar si no ejecutamos a pata el comando
        // Guardar los cambios en la base de datos
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}
