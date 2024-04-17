<?php

namespace App\Factory;

use App\Entity\SistemaOperativo;
use App\Repository\SistemaOperativoRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<SistemaOperativo>
 *
 * @method        SistemaOperativo|Proxy create(array|callable $attributes = [])
 * @method static SistemaOperativo|Proxy createOne(array $attributes = [])
 * @method static SistemaOperativo|Proxy find(object|array|mixed $criteria)
 * @method static SistemaOperativo|Proxy findOrCreate(array $attributes)
 * @method static SistemaOperativo|Proxy first(string $sortedField = 'id')
 * @method static SistemaOperativo|Proxy last(string $sortedField = 'id')
 * @method static SistemaOperativo|Proxy random(array $attributes = [])
 * @method static SistemaOperativo|Proxy randomOrCreate(array $attributes = [])
 * @method static SistemaOperativoRepository|RepositoryProxy repository()
 * @method static SistemaOperativo[]|Proxy[] all()
 * @method static SistemaOperativo[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static SistemaOperativo[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static SistemaOperativo[]|Proxy[] findBy(array $attributes)
 * @method static SistemaOperativo[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static SistemaOperativo[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class SistemaOperativoFactory extends ModelFactory
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

    const SISTEMAS = [
        'Windows',
        'Ubuntu',
    ];
    const VERSIONES = [
        '10',
        '20.04',
        '16.04',
        '7',
    ];
    protected function getDefaults(): array
    {
        return [
            'nombre' => self::faker()->randomElement(self::SISTEMAS),
            'version' => self::faker()->randomElement(self::VERSIONES),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(SistemaOperativo $sistemaOperativo): void {})
        ;
    }

    protected static function getClass(): string
    {
        return SistemaOperativo::class;
    }
}
