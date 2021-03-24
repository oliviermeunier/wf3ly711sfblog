<?php

namespace App\Service;

use App\Entity\Post;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostSlugUpdater {

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function updateSlug(Post $post)
    {
        $title = $post->getTitle();

        $slug = $this->slugger->slug($title);

        $post->setSlug($slug);
    }
}