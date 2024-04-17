<?php

namespace App\DataFixtures;

use App\Factory\AgenteFactory;
use App\Factory\ApiTokenFactory;
use App\Factory\EquipoFactory;
use App\Factory\IpAdressFactory;
use App\Factory\SectorFactory;
use App\Factory\SistemaOperativoFactory;
use App\Factory\SwitchesFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

      //  $manager->flush();
       // ProvinciaFactory::createMany(10);

        UserFactory::createMany(2);
        SectorFactory::createMany(4);
        SistemaOperativoFactory::createMany(6);
        EquipoFactory::createMany(6);
        AgenteFactory::createMany(6,function (){
            return [
                'sector' => SectorFactory::random(),
            ];
        });

        SwitchesFactory::createMany(6,function (){
            return [
                'sector' => SectorFactory::random(),
                'agente' => AgenteFactory::random(),
            ];
        });
        IpAdressFactory::createMany(2,function (){
            return [
                'agente' => AgenteFactory::random(),
                'switches' => SwitchesFactory::random(),
                'equipo' => EquipoFactory::random(),
            ];
        });
       ApiTokenFactory::createMany(6,function (){
           return [
               'ownedBy' => UserFactory::random(),
           ];
       });
    }


}
