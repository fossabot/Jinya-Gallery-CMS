<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 21:42
 */

namespace Jinya\Controller\Api\Gallery;

use Jinya\Formatter\Label\LabelFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtGalleryLabelController extends BaseApiController
{
    /**
     * @Route("/api/gallery/art/{slug}/label", methods={"GET"}, name="api_gallery_art_label_get")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param ArtGalleryServiceInterface $galleryService
     * @param LabelFormatterInterface $labelFormatter
     * @return Response
     */
    public function getAction(string $slug, ArtGalleryServiceInterface $galleryService, LabelFormatterInterface $labelFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService, $labelFormatter) {
            $gallery = $galleryService->get($slug);
            $labels = [];

            foreach ($gallery->getLabels() as $label) {
                $labels[] = $labelFormatter
                    ->init($label)
                    ->name()
                    ->format();
            }

            return $labels;
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/art/{slug}/label/{name}", methods={"PUT"}, name="api_gallery_art_label_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param string $name
     * @param ArtGalleryServiceInterface $galleryService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function putAction(string $slug, string $name, ArtGalleryServiceInterface $galleryService, LabelServiceInterface $labelService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $name, $galleryService, $labelService) {
            $gallery = $galleryService->get($slug);

            $gallery->getLabels()->add($labelService->getLabel($name));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/art/{slug}/label", methods={"PUT"}, name="api_gallery_art_label_put_batch")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param ArtGalleryServiceInterface $galleryService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function putBatchAction(string $slug, ArtGalleryServiceInterface $galleryService, LabelServiceInterface $labelService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService, $labelService) {
            $gallery = $galleryService->get($slug);
            $labels = $this->getValue('labels', []);

            foreach ($labels as $label) {
                $labelEntity = $labelService->getLabel($label);

                if (!$gallery->getLabels()->contains($labelEntity)) {
                    $gallery->getLabels()->add($labelEntity);
                }
            }

            $galleryService->saveOrUpdate($gallery);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/art/{slug}/label/{name}", methods={"DELETE"}, name="api_gallery_art_label_delete")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param string $name
     * @param ArtGalleryServiceInterface $galleryService
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function deleteAction(string $slug, string $name, ArtGalleryServiceInterface $galleryService, LabelServiceInterface $labelService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $name, $galleryService, $labelService) {
            $gallery = $galleryService->get($slug);

            $gallery->getLabels()->removeElement($labelService->getLabel($name));
            $galleryService->saveOrUpdate($gallery);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/art/{slug}/label", methods={"DELETE"}, name="api_gallery_art_label_delete_batch")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param string $slug
     * @param ArtGalleryServiceInterface $galleryService
     * @return Response
     */
    public function deleteBatchAction(string $slug, ArtGalleryServiceInterface $galleryService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $galleryService) {
            $gallery = $galleryService->get($slug);

            $gallery->getLabels()->clear();
            $galleryService->saveOrUpdate($gallery);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
