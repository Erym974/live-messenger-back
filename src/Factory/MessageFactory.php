<?php

namespace App\Factory;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\MessageStatus;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Message>
 *
 * @method        Message|Proxy create(array|callable $attributes = [])
 * @method static Message|Proxy createOne(array $attributes = [])
 * @method static Message|Proxy find(object|array|mixed $criteria)
 * @method static Message|Proxy findOrCreate(array $attributes)
 * @method static Message|Proxy first(string $sortedField = 'id')
 * @method static Message|Proxy last(string $sortedField = 'id')
 * @method static Message|Proxy random(array $attributes = [])
 * @method static Message|Proxy randomOrCreate(array $attributes = [])
 * @method static MessageRepository|RepositoryProxy repository()
 * @method static Message[]|Proxy[] all()
 * @method static Message[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Message[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Message[]|Proxy[] findBy(array $attributes)
 * @method static Message[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Message[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class MessageFactory extends ModelFactory
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
            'sended_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'content' => self::faker()->text(30),
            'status' =>  MessageStatus::SENDED,
            'edited' => false,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Message $message): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Message::class;
    }
}
