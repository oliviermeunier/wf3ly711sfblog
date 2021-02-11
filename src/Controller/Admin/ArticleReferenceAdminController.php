<?php

namespace App\Controller\Admin;

use App\Entity\ArticleReference;
use App\Entity\Post;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleReferenceAdminController extends AbstractController {

    /**
     * @Route("/admin/article/{id}/reference", name="admin.article.add.reference", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function uploadArticleReference(Post $post, Request $request, UploaderHelper $uploaderHelper, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('reference');

        $violations = $validator->validate($uploadedFile,
            [
                new File([
                    'maxSize' => '5M',
                    'maxSizeMessage' => 'Le fichier est trop volumineux. Maximum : 5Mo',
                    'mimeTypes' => [
                        'image/*',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain'
                    ],
                    'mimeTypesMessage' => 'Type de fichier invalide'
                ]),
                new NotBlank(['message' => 'Merci de sélectionner un fichier.'])
            ]
        );

        if ($violations->count() > 0) {

            return $this->json($violations, 400);

//            /** @var ConstraintViolation $violation */
//            $violation = $violations[0];

//            $this->addFlash('error', $violation->getMessage());
//
//            return $this->redirectToRoute('admin.post.edit', ['id' => $post->getId()]);
        }

        $filename = $uploaderHelper->uploadArticleReference($uploadedFile);

        $articleReference = new ArticleReference($post);
        $articleReference->setFilename($filename);
        $articleReference->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $filename);
        $articleReference->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');

        $manager->persist($articleReference);
        $manager->flush();

        return $this->json($articleReference, 201, [], ['groups' => ['main']]);

//        return $this->redirectToRoute('admin.post.edit', ['id' => $post->getId()]);
    }

    /**
     * @Route("/admin/article/{id}/reference", name="admin.article.list.references", methods="GET")
     * @IsGranted("ROLE_ADMIN")
     */
    public function getArticleReferences(Post $article)
    {
        return $this->json($article->getArticleReferences(), 200, [], ['groups' => ['main']]);
    }

    /**
     * @Route("/admin/article/references/{id}/download", name="admin.article.download.reference", methods={"GET"})
     */
    public function downloadArticleReference(ArticleReference $reference, UploaderHelper $uploaderHelper)
    {
        $response = new StreamedResponse(function() use ($reference, $uploaderHelper){
           $outputStream = fopen('php://output', 'wb');
           $fileStream = $uploaderHelper->readStream($reference->getFilePath(), false);
           stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', $reference->getMimeType());

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $reference->getOriginalFilename()
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @Route("/admin/article/references/{id}", name="admin.article.delete.reference", methods={"DELETE"})
     */
    public function deleteArticlereference(ArticleReference $reference, EntityManagerInterface $manager, UploaderHelper $uploaderHelper)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $manager->remove($reference);
        $manager->flush();

        $uploaderHelper->deleteFile($reference->getFilePath(), false);

        return new Response(null, 204);
    }
}