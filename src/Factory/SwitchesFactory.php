<?php

namespace App\Factory;

use App\Entity\Switches;
use App\Repository\SwitchesRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Switches>
 *
 * @method        Switches|Proxy create(array|callable $attributes = [])
 * @method static Switches|Proxy createOne(array $attributes = [])
 * @method static Switches|Proxy find(object|array|mixed $criteria)
 * @method static Switches|Proxy findOrCreate(array $attributes)
 * @method static Switches|Proxy first(string $sortedField = 'id')
 * @method static Switches|Proxy last(string $sortedField = 'id')
 * @method static Switches|Proxy random(array $attributes = [])
 * @method static Switches|Proxy randomOrCreate(array $attributes = [])
 * @method static SwitchesRepository|RepositoryProxy repository()
 * @method static Switches[]|Proxy[] all()
 * @method static Switches[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Switches[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Switches[]|Proxy[] findBy(array $attributes)
 * @method static Switches[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Switches[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class SwitchesFactory extends ModelFactory
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

    const MARCA = [
        'TP LINK',
        'ARUBA',
        '3COM'
    ];
    const MODELOS = [
        'SA-4321',
        'SE-1234',
    ];
    const ETIQUETA = [
        'SW-01 - A',
        'SW-02 - A',
        'SW-01 - B',
        'SW-01 - C',
        'SW-02 - C',
        'SW-03 - C',
        'SW-01 - D',
        'SW-01 - F',
        'SW-01 - E',
        'SW-01 - G',

    ];
    protected function getDefaults(): array
    {
        return [
            'estadoConexion' => self::faker()->boolean(),
            'marca' => self::faker()->randomElement(self::MARCA),
            'modelo' => self::faker()->randomElement(self::MODELOS),
            'etiqueta' => self::faker()->randomElement(self::ETIQUETA),
            'sector' => SectorFactory::new(),
            'agente' => AgenteFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Switches $switches): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Switches::class;
    }
}
