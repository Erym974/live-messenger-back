<?php

namespace App\Factory;

use App\Entity\Invitation;
use App\Repository\InvitationRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Invitation>
 *
 * @method        Invitation|Proxy create(array|callable $attributes = [])
 * @method static Invitation|Proxy createOne(array $attributes = [])
 * @method static Invitation|Proxy find(object|array|mixed $criteria)
 * @method static Invitation|Proxy findOrCreate(array $attributes)
 * @method static Invitation|Proxy first(string $sortedField = 'id')
 * @method static Invitation|Proxy last(string $sortedField = 'id')
 * @method static Invitation|Proxy random(array $attributes = [])
 * @method static Invitation|Proxy randomOrCreate(array $attributes = [])
 * @method static InvitationRepository|RepositoryProxy repository()
 * @method static Invitation[]|Proxy[] all()
 * @method static Invitation[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Invitation[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Invitation[]|Proxy[] findBy(array $attributes)
 * @method static Invitation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Invitation[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class InvitationFactory extends ModelFactory
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
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'emitter' => UserFactory::new(),
            'receiver' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Invitation $invitation): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Invitation::class;
    }
}
