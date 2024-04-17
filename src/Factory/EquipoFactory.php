<?php

namespace App\Factory;

use App\Entity\Equipo;
use App\Repository\EquipoRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Equipo>
 *
 * @method        Equipo|Proxy create(array|callable $attributes = [])
 * @method static Equipo|Proxy createOne(array $attributes = [])
 * @method static Equipo|Proxy find(object|array|mixed $criteria)
 * @method static Equipo|Proxy findOrCreate(array $attributes)
 * @method static Equipo|Proxy first(string $sortedField = 'id')
 * @method static Equipo|Proxy last(string $sortedField = 'id')
 * @method static Equipo|Proxy random(array $attributes = [])
 * @method static Equipo|Proxy randomOrCreate(array $attributes = [])
 * @method static EquipoRepository|RepositoryProxy repository()
 * @method static Equipo[]|Proxy[] all()
 * @method static Equipo[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Equipo[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Equipo[]|Proxy[] findBy(array $attributes)
 * @method static Equipo[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Equipo[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class EquipoFactory extends ModelFactory
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
        'Windows 7',
        'Windows 10',
        'Ubuntu 16.04',
        'Ubuntu 20.04',

    ];

    const NOMBRES = [
        'ComInt-01',
        'ComInt-02',
        'Logistica-01',
        'Logistica-02',
        'Logistica-03',
        'Informatica-01',
        'Informatica-02',
        'Informatica-03',
    ];
    const RAM = [
        '2000',
        '16000',
        '4000',
        '8000',
    ];
    protected function getDefaults(): array
    {
        return [
            'nombreDispositivo' => self::faker()->randomElement(self::NOMBRES),
            'sistemaOperativo' => self::faker()->randomElement(self::SISTEMAS),
            'memoriaRam' => self::faker()->randomElement(self::RAM),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Equipo $equipo): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Equipo::class;
    }
}
