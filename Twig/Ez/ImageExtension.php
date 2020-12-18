<?php

namespace Gie\EzProgressiveImageBundle\Twig\Ez;

use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ImageExtension as EzImageExtension;
use eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPathGenerator;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\SPI\Variation\Values\ImageVariation;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;

class ImageExtension extends EzImageExtension
{

    /**
     * @var \eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPathGenerator
     */
    private $variationPathGenerator;

    /**
     * @var \eZ\Publish\Core\IO\IOServiceInterface
     */
    private $ioService;

    /**
     * @var string $placeholderSuffix
     */
    protected $placeholderSuffix;

    /**
     * @param VariationPathGenerator $variationPathGenerator
     * @param IOServiceInterface $ioService
     * @param string $placeholderVariationName
     */
    public function setConfig(
        VariationPathGenerator $variationPathGenerator,
        IOServiceInterface $ioService,
        string $placeholderSuffix
    ) {
        $this->variationPathGenerator = $variationPathGenerator;
        $this->ioService = $ioService;
        $this->placeholderSuffix = $placeholderSuffix;
    }

    /**
     * Returns the image variation object for $field/$versionInfo.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Field $field
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $variationName
     *
     * @return \eZ\Publish\SPI\Variation\Values\Variation|null
     * @throws InvalidArgumentValue
     * @throws NotFoundException
     */
    public function getImageVariation(Field $field, VersionInfo $versionInfo, $variationName)
    {
        $imageVariation = null;
        $base64 = 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';

        try {
            /** @var ImageVariation $imageVariation */
            $imageVariation = parent::getImageVariation($field, $versionInfo, $variationName);
        } finally {
            if (!$imageVariation) {
                return new ImageVariation(
                    [
                        'width'     => 1,
                        'height'    => 1,
                        'uri'       => $base64,
                        'info'      => $base64,
                    ]
                );
            }
        }

        $base64 = $this->getBase64($field->value->id, $variationName);

        return new ImageVariation(
            [
                'width'         => $imageVariation->width ?? 0,
                'height'        => $imageVariation->height ?? 0,
                'name'          => $imageVariation->name ?? '',
                'imageId'       => $imageVariation->imageId,
                'uri'           => preg_replace('/(https?:\/\/[^:\/]+:?(\d+)?)/', '', $imageVariation->uri),
                'dirPath'       => $imageVariation->dirPath,
                'fileName'      => $imageVariation->fileName,
                'fileSize'      => $imageVariation->fileSize,
                'mimeType'      => $imageVariation->mimeType,
                'lastModified'  => $imageVariation->lastModified,
                'info'          => $base64,
            ]
        );
    }

    /**
     * @param string $originalPath
     * @param string $variationName
     * @return string|null
     * @throws InvalidArgumentValue
     * @throws NotFoundException
     */
    private function getBase64(string $originalPath, string $variationName)
    {
        if (strpos($variationName, $this->placeholderSuffix)) {
            $variationPath = $this->variationPathGenerator->getVariationPath(
                $originalPath,
                $variationName
            );

            $variationBinaryFile = $this->ioService->loadBinaryFile($variationPath);

            return base64_encode($this->ioService->getFileContents($variationBinaryFile));
        }

        return null;
    }
}