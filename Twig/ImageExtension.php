<?php

namespace Gie\EzProgressiveImageBundle\Twig;

use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ImageExtension as BaseImageExtension;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\SPI\Variation\Values\ImageVariation;

class ImageExtension extends BaseImageExtension
{
    /**
     * @var string $webDir
     */
    protected $webDir;

    /**
     * @var string $placeholderVariationName
     */
    protected $placeholderVariationName;

    /**
     * @param string $webDir
     * @param string $placeholderVariationName
     */
    public function setConfig(string $webDir, string $placeholderVariationName)
    {
        $this->webDir = $webDir;
        $this->placeholderVariationName = $placeholderVariationName;
    }

    /**
     * Returns the image variation object for $field/$versionInfo.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Field $field
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $variationName
     *
     * @return \eZ\Publish\SPI\Variation\Values\Variation|null
     */
    public function getImageVariation(Field $field, VersionInfo $versionInfo, $variationName)
    {
        /** @var ImageVariation $imageVariation */
        $imageVariation = parent::getImageVariation($field, $versionInfo, $variationName);
        $imagePath = $this->webDir . parse_url($imageVariation->uri, PHP_URL_PATH);
        $imageInfo = getimagesize($imagePath);

        $base64 = $variationName === $this->placeholderVariationName
            ? base64_encode(file_get_contents($imagePath))
            : null;

        return new ImageVariation(
            [
                'name' => $imageVariation->name,
                'fileName' => $imageVariation->fileName,
                'dirPath' => $imageVariation->dirPath,
                'uri' => preg_replace('/https?:/', '', $imageVariation->uri),
                'imageId' => $imageVariation->imageId,
                'mimeType' => $imageInfo['mime'],
                'width' => $imageVariation->width ?? $imageInfo[0],
                'height' => $imageVariation->height ?? $imageInfo[1],
                'info' => [
                    'style' => $imageInfo[3],
                    'base64' => $base64,
                ]
            ]
        );
    }
}