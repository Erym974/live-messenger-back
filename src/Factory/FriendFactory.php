<?php

namespace App\Factory;

use App\Entity\Friend;
use App\Repository\FriendRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Friend>
 *
 * @method        Friend|Proxy create(array|callable $attributes = [])
 * @method static Friend|Proxy createOne(array $attributes = [])
 * @method static Friend|Proxy find(object|array|mixed $criteria)
 * @method static Friend|Proxy findOrCreate(array $attributes)
 * @method static Friend|Proxy first(string $sortedField = 'id')
 * @method static Friend|Proxy last(string $sortedField = 'id')
 * @method static Friend|Proxy random(array $attributes = [])
 * @method static Friend|Proxy randomOrCreate(array $attributes = [])
 * @method static FriendRepository|RepositoryProxy repository()
 * @method static Friend[]|Proxy[] all()
 * @method static Friend[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Friend[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Friend[]|Proxy[] findBy(array $attributes)
 * @method static Friend[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Friend[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class FriendFactory extends ModelFactory
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
            'conversation' => GroupFactory::new(),
            'friend' => UserFactory::new(),
            'since' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Friend $friend): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Friend::class;
    }
}
