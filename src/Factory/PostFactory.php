<?php

namespace App\Factory;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Post|Proxy findOrCreate(array $attributes)
 * @method static Post|Proxy random()
 * @method static Post[]|Proxy[] randomSet(int $number)
 * @method static Post[]|Proxy[] randomRange(int $min, int $max)
 * @method static PostRepository|RepositoryProxy repository()
 * @method Post|Proxy create($attributes = [])
 * @method Post[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class PostFactory extends ModelFactory
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)

        $this->slugger = $slugger;
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->sentence(),
            'content' => self::faker()->text(2000),
            'image' => 'https://picsum.photos/seed/post' .rand(0,1000). '/750/300',
            'createdAt' => self::faker()->dateTimeBetween('-3 years', 'now', 'Europe/Paris'),
            'category' => CategoryFactory::random(),
            'user' => UserFactory::findOrCreate(['email' => 'admin@admin.com'])
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            ->afterInstantiate(function(Post $post) {

                $title = $post->getTitle();
                $slug = $this->slugger->slug($title);

                /**
                 * une fois l'entité Post créée, on va intervenir dessus pour générer le slug à partir du titre (du champ title)
                 */
                $post->setSlug($slug);
            })
        ;
    }

    protected static function getClass(): string
    {
        return Post::class;
    }
}
