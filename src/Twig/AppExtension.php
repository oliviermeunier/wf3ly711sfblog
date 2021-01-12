<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
//    public function getFilters(): array
//    {
//        return [
//            // If your filter generates SAFE HTML, you should add a third
//            // parameter: ['is_safe' => ['html']]
//            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
//            new TwigFilter('filter_name', [$this, 'doSomething']),
//        ];
//    }


    /**
     * @var string
     */
    private $postImageUrl;

    public function __construct(string $postImageUrl)
    {
        $this->postImageUrl = $postImageUrl;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_post_image', [$this, 'assetPostImage']),
        ];
    }

    public function assetPostImage($imageFilename)
    {
        // Si c'est une URL qui est enregistrée (notamment pour les fixtures)
        if (filter_var($imageFilename, FILTER_VALIDATE_URL)) {
            return $imageFilename;
        }

        // Sinon (c'est le nom d'un fichier uploadé)
        return $this->postImageUrl . '/' . $imageFilename;
    }
}
