<?php

namespace App\Factory;

use App\Entity\Agente;
use App\Repository\AgenteRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Agente>
 *
 * @method        Agente|Proxy create(array|callable $attributes = [])
 * @method static Agente|Proxy createOne(array $attributes = [])
 * @method static Agente|Proxy find(object|array|mixed $criteria)
 * @method static Agente|Proxy findOrCreate(array $attributes)
 * @method static Agente|Proxy first(string $sortedField = 'id')
 * @method static Agente|Proxy last(string $sortedField = 'id')
 * @method static Agente|Proxy random(array $attributes = [])
 * @method static Agente|Proxy randomOrCreate(array $attributes = [])
 * @method static AgenteRepository|RepositoryProxy repository()
 * @method static Agente[]|Proxy[] all()
 * @method static Agente[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Agente[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Agente[]|Proxy[] findBy(array $attributes)
 * @method static Agente[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Agente[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class AgenteFactory extends ModelFactory
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

    const NOMBRES = [
        'Joaquin',
        'Milton',
    ];
    const APELLIDOS = [
        'Luengo',
        'Bugallo',
    
    ];
    protected function getDefaults(): array
    {
        return [
            'apellido' => self::faker()->randomElement(self::APELLIDOS),
            'nombre' => self::faker()->randomElement(self::NOMBRES),
            'sector' => SectorFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Agente $agente): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Agente::class;
    }
}
