<?php

namespace App\Service;


use App\Entity\Post;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderHelper {

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var string
     */
    private $postImageDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;
    private $privateFilesystem;
    private $publicFilesystem;

    const ARTICLE_REFERENCE = 'article_reference';

    public function __construct(SluggerInterface $slugger, string $postImageDirectory, Filesystem $filesystem, $privateUploadsFilesystem, $publicUploadsFilesystem)
    {
        $this->slugger = $slugger;
        $this->postImageDirectory = $postImageDirectory;
        $this->filesystem = $filesystem;
        $this->privateFilesystem = $privateUploadsFilesystem;
        $this->publicFilesystem = $publicUploadsFilesystem;
    }

    public function uploadPostImage(Post $post, ?UploadedFile $uploadedFile)
    {
        // Si l'administrateur a rempli le champ image...
        if ($uploadedFile) {

            // Suppression de l'image actuelle le cas échéant
            $this->removePostImageFile($post);

            // Normalisation du nom du fichier image
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $this->slugger->slug($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            $post->setImage($newFilename);

            // Copie du fichier temporaire vers le répertoire de destination
            $uploadedFile->move($this->postImageDirectory, $newFilename);
        }
    }

    public function removePostImageFile(Post $post)
    {
        // Si un fichier image existe déjà (càd si le champ image de l'entité $post est rempli) ...
        if ($imageFilename = $post->getImage()) {

            // ... alors on supprimer l'ancien fichier
            $currentPath = $this->postImageDirectory . '/' . $imageFilename;
            if ($this->filesystem->exists($currentPath)) {
                $this->filesystem->remove($currentPath);
            }
        }
    }

    public function uploadArticleReference(File $file): string
    {
        return $this->uploadFile($file, self::ARTICLE_REFERENCE, false);
    }

    private function uploadFile(File $file, string $directory, bool $isPublic): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = $this->slugger->slug($originalFilename) . '-' . uniqid() . '.' . $file->guessExtension();
        $stream = fopen($file->getPathname(), 'r');

        $filesystem = $isPublic ? $this->publicFilesystem : $this->privateFilesystem;

        $result = $filesystem->writeStream(
            $directory.'/'.$newFilename,
            $stream
        );

        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFilename;
    }

    /**
     * @return resource
     */
    public function readStream(string $path, bool $isPublic)
    {
        $filesystem = $isPublic ? $this->publicFilesystem : $this->privateFilesystem;

        $resource = $filesystem->readStream($path);

        if ($resource === false) {
            throw new \Exception(sprintf('Error opening stream for "%s"', $path));
        }

        return $resource;
    }


    public function deleteFile(string $path, bool $isPublic)
    {
        $filesystem = $isPublic ? $this->publicFilesystem : $this->privateFilesystem;
        $result = $filesystem->delete($path);
        if ($result === false) {
            throw new \Exception(sprintf('Error deleting "%s"', $path));
        }
    }
}