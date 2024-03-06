<?php

namespace App\Factory;

use App\Entity\Job;
use App\Repository\JobRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Job>
 *
 * @method        Job|Proxy create(array|callable $attributes = [])
 * @method static Job|Proxy createOne(array $attributes = [])
 * @method static Job|Proxy find(object|array|mixed $criteria)
 * @method static Job|Proxy findOrCreate(array $attributes)
 * @method static Job|Proxy first(string $sortedField = 'id')
 * @method static Job|Proxy last(string $sortedField = 'id')
 * @method static Job|Proxy random(array $attributes = [])
 * @method static Job|Proxy randomOrCreate(array $attributes = [])
 * @method static JobRepository|RepositoryProxy repository()
 * @method static Job[]|Proxy[] all()
 * @method static Job[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Job[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Job[]|Proxy[] findBy(array $attributes)
 * @method static Job[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Job[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class JobFactory extends ModelFactory
{

    private array $categories = [
        'Core Tech Engineering',
        'Activities Platform',
        'Data Platform Engineering',
        'Communications & PR',
        'Machine Learning',
        'Product Management'
    ];

    private array $locations = [
        'Remote',
        'On-site',
        'Hybrid',
    ];

    private array $prefix = [
        'Senior',
        'Junior',
        'Lead'
    ];

    private array $jobs = [
        'Engineer',
        'Manager',
        'Developer',
        'Designer',
        'Analyst',
    ];

    private array $requirements = [
        "Bachelor's degree in relevant field or equivalent experience.",
        "Proficiency in programming languages/tools/software.",
        "Strong analytical and problem-solving skills.",
        "Excellent communication skills, both verbal and written.",
        "Ability to work independently and in a team environment.",
        "Detail-oriented with a focus on quality and accuracy.",
        "Proven track record of meeting deadlines and delivering results.",
        "Flexibility to adapt to changing priorities and multitask effectively.",
        "Experience with specific industry or domain knowledge.",
        "Knowledge of regulatory requirements, if applicable.",
        "Willingness to travel, if required.",
        "Certification in relevant certification, if applicable.",
        "Familiarity with specific methodologies or frameworks.",
        "Ability to collaborate with cross-functional teams.",
        "Strong organizational skills and ability to prioritize tasks.",
        "Experience with specific tools or technologies.",
        "Ability to learn new technologies quickly.",
        "Demonstrated ability to lead projects or teams, if applicable.",
        "Commitment to continuous learning and professional development."
    ];

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
            'category' => self::faker()->randomElement($this->categories),
            'location' => self::faker()->randomElement($this->locations),
            'long_description' => self::faker()->text(),
            'short_description' => self::faker()->text(100),
            'title' => self::faker()->randomElement($this->prefix) . " " . self::faker()->randomElement($this->jobs),
            'requirements' => self::faker()->randomElements($this->requirements, self::faker()->numberBetween(1, 5)),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Job $job): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Job::class;
    }
}
