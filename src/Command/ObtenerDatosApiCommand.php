<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\HttpClient\HttpClient;
use App\Entity\Equipo;
use App\Entity\IpAdress;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:obtener-datos-api',
    description: 'Add a short description for your command',
)]
class ObtenerDatosApiCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Obtener datos de la API')
            ->setHelp('Este comando obtiene datos de la API y los guarda en la base de datos.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = HttpClient::create();
        $direccionesIP = [];

        for ($i = 1; $i <= 4000; $i++) {
            // Define la URL del endpoint para el equipo actual
            $url = 'http://10.7.6.7/ocsapi/v1/computer/' . $i . '/hardware';
            $url_mac =  'http://10.7.6.7/ocsapi/v1/computer/' . $i . '/networks';
            // Realiza la solicitud GET
            $response = $client->request('GET', $url);
            //Obtengo la MAC de otro ENDPOINT
            $response_mac = $client->request('GET', $url_mac);
            // Verifica si la solicitud fue exitosa (código de respuesta 200)
            if ($response->getStatusCode() === 200 && $response_mac->getStatusCode() === 200 ) {
                // Convierte la respuesta JSON en un array asociativo de PHP
                $content = $response->getContent();
                $content_mac = $response_mac->getContent();
                if ($content != "null" && $content_mac != "null" ) {
                    $data = $response->toArray();
                    $data_mac = $response_mac->toArray();

                    // Accede a la dirección IP (IPADDR) y guárdala en el arreglo
                    if (isset($data[$i]['hardware']['IPADDR']) && isset($data_mac[$i]['networks'][0]['MACADDR'])) {
                       // $output->writeln($data_mac[$i]['networks'][0]['MACADDR']);
                        $patron = '/^(?:\d{1,3}\.){3}\d{1,3}$/';
                        if( preg_match($patron, $data[$i]['hardware']['IPADDR']) != 1 ) { continue;}

                        $direccionIP = $data[$i]['hardware']['IPADDR'];
                        $direccionesIP[] = $direccionIP;
                        // Busca si ya existe una entidad IPAddress con esta dirección IP
                        $ipAddressExistente = $this->entityManager->getRepository(IpAdress::class)
                            ->findOneBy(['direccion' => $direccionIP]);

                        $equipo = $this->entityManager->getRepository(Equipo::class)
                            ->findOneBy([
                                'nombreDispositivo' => $data[$i]['hardware']['NAME'],
                                'macaddress' => $data_mac[$i]['networks'][0]['MACADDR']
                            ]);


                        if ($equipo) {
                            //Como un equipo no puede tener dos IPs tengo q borrar en la Tabla Ipaddress el equipo
                            //que estaba asigando y asginarselo a la nueva IP
                            //Esto pasa porque una PC conectada por wifi cambia de IP cada tanto
                            $deleteEquipo = $this->entityManager->getRepository(IpAdress::class)
                                ->findOneBy(['equipo' => $equipo]);

                            if ($deleteEquipo) {

                              //  $output->writeln($i)    ;
                                $deleteEquipo->setEquipo(null);
                                //CHEQUEAR SI TIENE REALACION INVERSA PARA SACARLE LA IPADRES DEL OTRO LADO

                                $this->entityManager->persist($deleteEquipo);
                                $this->entityManager->flush();
                                // Resto del código
                            }
                            if($data[$i]['hardware']['OSNAME'] == "Ubuntu"){
                                $equipo->setSistemaOperativo($data[$i]['hardware']['OSNAME']. ' '.$data[$i]['hardware']['OSVERSION']);}
                        } else {
                            $equipo = new Equipo();
                            $equipo->setNombreDispositivo($data[$i]['hardware']['NAME']);

                            if($data[$i]['hardware']['OSNAME'] == "Ubuntu"){
                                $equipo->setSistemaOperativo($data[$i]['hardware']['OSNAME']. ' '.$data[$i]['hardware']['OSVERSION']);}
                            else{
                                $equipo->setSistemaOperativo($data[$i]['hardware']['OSNAME']);}

                            $equipo->setMemoriaRam($data[$i]['hardware']['MEMORY']);
                            $equipo->setMacaddress($data_mac[$i]['networks'][0]['MACADDR']);
                        }
                        // Verifica si la entidad IPAddress existe
                        if ($ipAddressExistente) {
                            //Si algun equipo tenia la IP directamente la piso
                            $ipAddressExistente->setEquipo($equipo);

                        } else {
                            //$output->writeln($data[$i]['hardware']['IPADDR']);
                            $ipAddressExistente = new IpAdress();
                            $ipAddressExistente->setDireccion($direccionIP);
                            $ipAddressExistente->setEquipo($equipo);
                        }

                        // Guarda las entidades en la base de datos
                        $this->entityManager->persist($equipo);
                        $this->entityManager->persist($ipAddressExistente);

                        $this->entityManager->flush();
                    }
                }
            } else {
                // Manejo de errores...
            }
        }

        $output->writeln('Datos de la API obtenidos y guardados en la base de datos.');

        return Command::SUCCESS;
    }
}
