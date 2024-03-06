<?php

namespace App\Factory;

use App\Entity\Meta;
use App\Repository\MetaRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Meta>
 *
 * @method        Meta|Proxy create(array|callable $attributes = [])
 * @method static Meta|Proxy createOne(array $attributes = [])
 * @method static Meta|Proxy find(object|array|mixed $criteria)
 * @method static Meta|Proxy findOrCreate(array $attributes)
 * @method static Meta|Proxy first(string $sortedField = 'id')
 * @method static Meta|Proxy last(string $sortedField = 'id')
 * @method static Meta|Proxy random(array $attributes = [])
 * @method static Meta|Proxy randomOrCreate(array $attributes = [])
 * @method static MetaRepository|RepositoryProxy repository()
 * @method static Meta[]|Proxy[] all()
 * @method static Meta[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Meta[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Meta[]|Proxy[] findBy(array $attributes)
 * @method static Meta[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Meta[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class MetaFactory extends ModelFactory
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
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->text(255),
            'value' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Meta $meta): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Meta::class;
    }
}
