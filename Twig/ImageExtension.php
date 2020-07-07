<?php

namespace Gie\EzProgressiveImageBundle\Twig;

use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ImageExtension as BaseImageExtension;
use eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPathGenerator;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\SPI\Variation\Values\ImageVariation;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;

class ImageExtension extends BaseImageExtension
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
     * @var string $placeholderVariationName
     */
    protected $placeholderVariationName;

    /**
     * @param VariationPathGenerator $variationPathGenerator
     * @param IOServiceInterface $ioService
     * @param string $placeholderVariationName
     */
    public function setConfig(
        VariationPathGenerator $variationPathGenerator,
        IOServiceInterface $ioService,
        string $placeholderVariationName
    ) {
        $this->variationPathGenerator = $variationPathGenerator;
        $this->ioService = $ioService;
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
     * @throws InvalidArgumentValue
     * @throws NotFoundException
     */
    public function getImageVariation(Field $field, VersionInfo $versionInfo, $variationName)
    {
        /** @var ImageVariation $imageVariation */
        $imageVariation = parent::getImageVariation($field, $versionInfo, $variationName);
        $base64 = $this->getBase64($field->value->id, $variationName);

        return new ImageVariation(
            [
                'width'         => $imageVariation->width,
                'height'        => $imageVariation->height,
                'name'          => $imageVariation->name,
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
        if ($variationName === $this->placeholderVariationName) {
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