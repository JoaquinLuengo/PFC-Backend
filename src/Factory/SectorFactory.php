<?php

namespace App\Factory;

use App\Entity\Sector;
use App\Repository\SectorRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Sector>
 *
 * @method        Sector|Proxy create(array|callable $attributes = [])
 * @method static Sector|Proxy createOne(array $attributes = [])
 * @method static Sector|Proxy find(object|array|mixed $criteria)
 * @method static Sector|Proxy findOrCreate(array $attributes)
 * @method static Sector|Proxy first(string $sortedField = 'id')
 * @method static Sector|Proxy last(string $sortedField = 'id')
 * @method static Sector|Proxy random(array $attributes = [])
 * @method static Sector|Proxy randomOrCreate(array $attributes = [])
 * @method static SectorRepository|RepositoryProxy repository()
 * @method static Sector[]|Proxy[] all()
 * @method static Sector[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Sector[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Sector[]|Proxy[] findBy(array $attributes)
 * @method static Sector[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Sector[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class SectorFactory extends ModelFactory
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

    const SECTORES = [
        'Logistica',
        'Informatica',
        'Administracion',
        'Suelos',
    ];
    protected function getDefaults(): array
    {
        return [
            'nombre' => self::faker()->randomElement(self::SECTORES),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Sector $sector): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Sector::class;
    }
}
