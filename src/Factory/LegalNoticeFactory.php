<?php

namespace App\Factory;

use App\Entity\LegalNotice;
use App\Repository\LegalNoticeRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<LegalNotice>
 *
 * @method        LegalNotice|Proxy create(array|callable $attributes = [])
 * @method static LegalNotice|Proxy createOne(array $attributes = [])
 * @method static LegalNotice|Proxy find(object|array|mixed $criteria)
 * @method static LegalNotice|Proxy findOrCreate(array $attributes)
 * @method static LegalNotice|Proxy first(string $sortedField = 'id')
 * @method static LegalNotice|Proxy last(string $sortedField = 'id')
 * @method static LegalNotice|Proxy random(array $attributes = [])
 * @method static LegalNotice|Proxy randomOrCreate(array $attributes = [])
 * @method static LegalNoticeRepository|RepositoryProxy repository()
 * @method static LegalNotice[]|Proxy[] all()
 * @method static LegalNotice[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static LegalNotice[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static LegalNotice[]|Proxy[] findBy(array $attributes)
 * @method static LegalNotice[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static LegalNotice[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class LegalNoticeFactory extends ModelFactory
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
            'locale' => self::faker()->text(5),
            'type' => self::faker()->text(15),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(LegalNotice $legalNotice): void {})
        ;
    }

    protected static function getClass(): string
    {
        return LegalNotice::class;
    }
}
