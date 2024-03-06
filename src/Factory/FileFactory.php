<?php

namespace App\Factory;

use App\Entity\File;
use App\Repository\FileRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<File>
 *
 * @method        File|Proxy create(array|callable $attributes = [])
 * @method static File|Proxy createOne(array $attributes = [])
 * @method static File|Proxy find(object|array|mixed $criteria)
 * @method static File|Proxy findOrCreate(array $attributes)
 * @method static File|Proxy first(string $sortedField = 'id')
 * @method static File|Proxy last(string $sortedField = 'id')
 * @method static File|Proxy random(array $attributes = [])
 * @method static File|Proxy randomOrCreate(array $attributes = [])
 * @method static FileRepository|RepositoryProxy repository()
 * @method static File[]|Proxy[] all()
 * @method static File[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static File[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static File[]|Proxy[] findBy(array $attributes)
 * @method static File[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static File[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class FileFactory extends ModelFactory
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
            'parent' => self::faker()->text(50),
            'path' => self::faker()->text(255),
            'type' => self::faker()->text(50),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(File $file): void {})
        ;
    }

    protected static function getClass(): string
    {
        return File::class;
    }
}
