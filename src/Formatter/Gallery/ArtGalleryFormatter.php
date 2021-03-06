<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:52
 */

namespace Jinya\Formatter\Gallery;

use Jinya\Entity\Artwork\ArtworkPosition;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Formatter\Artwork\ArtworkPositionFormatterInterface;
use Jinya\Formatter\User\UserFormatterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArtGalleryFormatter implements ArtGalleryFormatterInterface
{
    /** @var ArtGallery */
    private $gallery;

    /** @var array */
    private $formattedData;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var ArtworkPositionFormatterInterface */
    private $artworkPositionFormatter;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * GalleryFormatter constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param UserFormatterInterface $userFormatter
     */
    public function setUserFormatter(UserFormatterInterface $userFormatter): void
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * @param ArtworkPositionFormatterInterface $artworkPositionFormatter
     */
    public function setArtworkPositionFormatter(ArtworkPositionFormatterInterface $artworkPositionFormatter): void
    {
        $this->artworkPositionFormatter = $artworkPositionFormatter;
    }

    /**
     * Formats the content of the @see FormatterInterface into an array
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the formatting
     *
     * @param ArtGallery $gallery
     * @return ArtGalleryFormatterInterface
     */
    public function init(ArtGallery $gallery): ArtGalleryFormatterInterface
    {
        $this->gallery = $gallery;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the slug
     *
     * @return ArtGalleryFormatterInterface
     */
    public function slug(): ArtGalleryFormatterInterface
    {
        $this->formattedData['slug'] = $this->gallery->getSlug();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return ArtGalleryFormatterInterface
     */
    public function name(): ArtGalleryFormatterInterface
    {
        $this->formattedData['name'] = $this->gallery->getName();

        return $this;
    }

    /**
     * Formats the description
     *
     * @return ArtGalleryFormatterInterface
     */
    public function description(): ArtGalleryFormatterInterface
    {
        $this->formattedData['description'] = $this->gallery->getDescription();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return ArtGalleryFormatterInterface
     */
    public function created(): ArtGalleryFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->gallery->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->gallery->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return ArtGalleryFormatterInterface
     */
    public function updated(): ArtGalleryFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->gallery->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->gallery->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return ArtGalleryFormatterInterface
     */
    public function history(): ArtGalleryFormatterInterface
    {
        $this->formattedData['history'] = $this->gallery->getHistory();

        return $this;
    }

    /**
     * Formats the orientation
     *
     * @return ArtGalleryFormatterInterface
     */
    public function orientation(): ArtGalleryFormatterInterface
    {
        $this->formattedData['orientation'] = $this->gallery->getOrientation();

        return $this;
    }

    /**
     * Formats the artworks
     *
     * @return ArtGalleryFormatterInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function artworks(): ArtGalleryFormatterInterface
    {
        $this->formattedData['artworks'] = [];

        $artworkPositions = $this->gallery->getArtworks()->toArray();
        uasort($artworkPositions, function (ArtworkPosition $a, ArtworkPosition $b) {
            return $a->getPosition() > $b->getPosition();
        });

        $artworks = array_values($artworkPositions);

        /** @var ArtworkPosition $artworkPosition */
        foreach ($artworks as $artworkPosition) {
            $this->formattedData['artworks'][] = $this->artworkPositionFormatter
                ->init($artworkPosition)
                ->position()
                ->id()
                ->artwork()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the background
     *
     * @return ArtGalleryFormatterInterface
     */
    public function background(): ArtGalleryFormatterInterface
    {
        $this->formattedData['background'] = $this->urlGenerator->generate('api_gallery_art_background_get', ['slug' => $this->gallery->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this;
    }

    /**
     * Formats the labels
     *
     * @return ArtGalleryFormatterInterface
     */
    public function labels(): ArtGalleryFormatterInterface
    {
        $this->formattedData['labels'] = [];

        foreach ($this->gallery->getLabels() as $label) {
            $this->formattedData['labels'][] = ['name' => $label->getName()];
        }

        return $this;
    }

    /**
     * Formats the id
     *
     * @return ArtGalleryFormatterInterface
     */
    public function id(): ArtGalleryFormatterInterface
    {
        $this->formattedData['id'] = $this->gallery->getId();

        return $this;
    }
}
