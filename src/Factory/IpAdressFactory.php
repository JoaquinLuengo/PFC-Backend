<?php

namespace App\Factory;

use App\Entity\IpAdress;
use App\Repository\IpAdressRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<IpAdress>
 *
 * @method        IpAdress|Proxy create(array|callable $attributes = [])
 * @method static IpAdress|Proxy createOne(array $attributes = [])
 * @method static IpAdress|Proxy find(object|array|mixed $criteria)
 * @method static IpAdress|Proxy findOrCreate(array $attributes)
 * @method static IpAdress|Proxy first(string $sortedField = 'id')
 * @method static IpAdress|Proxy last(string $sortedField = 'id')
 * @method static IpAdress|Proxy random(array $attributes = [])
 * @method static IpAdress|Proxy randomOrCreate(array $attributes = [])
 * @method static IpAdressRepository|RepositoryProxy repository()
 * @method static IpAdress[]|Proxy[] all()
 * @method static IpAdress[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static IpAdress[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static IpAdress[]|Proxy[] findBy(array $attributes)
 * @method static IpAdress[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static IpAdress[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class IpAdressFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    const DIRECCIONES = [
        '10.7.7.0',
        '10.7.7.1',
        '10.7.7.2',
        '10.7.7.3',
        '10.7.7.4',
        '10.7.7.14',
        '10.7.7.125',
        '10.7.7.200',
        '10.7.7.255',

        '10.7.6.0',
        '10.7.6.1',
        '10.7.6.2',
        '10.7.6.3',
        '10.7.6.4',
        '10.7.6.14',
        '10.7.6.125',
        '10.7.6.200',
        '10.7.6.255',

    ];

    protected function getDefaults(): array
    {
        // Asegúrate de que las direcciones disponibles estén definidas en el orden correcto
        $direccionesDisponibles = self::DIRECCIONES;

        // Mezcla aleatoriamente las direcciones IP disponibles
        shuffle($direccionesDisponibles);

        return [
            'direccion' => self::faker()->unique()->randomElement($direccionesDisponibles),
            'agente' => AgenteFactory::new(),
            'switches' => SwitchesFactory::new(),
            'equipo' => EquipoFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(IpAdress $ipAdress): void {})
        ;
    }

    protected static function getClass(): string
    {
        return IpAdress::class;
    }
}
