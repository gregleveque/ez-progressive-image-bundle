<?php


namespace Gie\EzProgressiveImageBundle\Twig\Utility;


use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\FieldType\ImageAsset\AssetMapper;
use Gie\EzProgressiveImageBundle\Twig\Ez\ImageExtension;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class ImageRuntime implements RuntimeExtensionInterface
{
    /** @var array  */
    const DEFAULT_PARAMS = [
        'class' => '',
        'alt' => '',
        'parent-fit' => 'cover',
        'object-fit' => 'cover',
        'caption' => '',
        'captionFieldIdentifier' => 'caption',
        'styles' => '',
    ];

    /** @var \Twig\Environment */
    private $twig;

    /** @var \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper */
    private $mapper;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \Gie\EzProgressiveImageBundle\Twig\Ez\ImageExtension  */
    private $imageExtension;
    /**
     * ImageRuntime constructor.
     * @param \Twig\Environment $twig
     * @param \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper $mapper\
     */
    public function __construct(
        Environment $twig,
        AssetMapper $mapper,
        ContentService $contentService,
        ImageExtension $imageExtension
    ) {
        $this->twig = $twig;
        $this->mapper = $mapper;
        $this->contentService = $contentService;
        $this->imageExtension = $imageExtension;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string $fieldIdentifier
     * @param $config
     * @param array $params
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function render(Content $content, string $fieldIdentifier, $config, array $params = [])
    {
        $field = $content->getField($fieldIdentifier);

        if (!in_array($field->fieldTypeIdentifier, ['ezimage', 'ezimageasset'])) {
            throw new InvalidArgumentException('fieldIndentifier', "Field $fieldIdentifier is not an image field or an image asset field.");
        }

        if ($field->fieldTypeIdentifier === 'ezimageasset' && $field->value->destinationContentId) {
            $content = $this->contentService->loadContent((int)$field->value->destinationContentId);
            $fieldIdentifier = $this->mapper->getContentFieldIdentifier($content);
        }

        $alt = $content->getFieldValue($fieldIdentifier)->alternativeText;
        $config = $this->getConfig($config);

        if ($params['raw'] ?? false) {
            $field = $content->getField($fieldIdentifier);
            $versionInfo = $content->getVersionInfo();
            // To have all aliases generated
            $aliases = [
                'low' => $this->imageExtension->getImageVariation($field, $versionInfo, $config['default'] . '.placeholder'),
                '1x' =>  $this->imageExtension->getImageVariation($field, $versionInfo, $config['default']),
                '2x' => $this->imageExtension->getImageVariation($field, $versionInfo, $config['default'] . '.2x'),
                '3x' => $this->imageExtension->getImageVariation($field, $versionInfo, $config['default'] . '.3x'),
            ];

            return [
                'alt' => $alt,
                'size' => [$aliases['1x']->width, $aliases['1x']->height],
                'src' => str_replace('', '%20', $aliases['1x']->uri),
                'srcset' => [$aliases['2x']->width . 'w', $aliases['3x']->width . 'w'],
                'low' => 'data:' . $aliases['low']->mimeType . ';base64,' . $aliases['low']->info,
            ];
        } else {
            return $this->twig->render('@ezdesign/ezprogressiveimage/image.html.twig', [
                'content' => $content,
                'identifier' => $fieldIdentifier,
                'config' => $config,
                'parameters' => array_replace_recursive(self::DEFAULT_PARAMS, ['alt' => $alt], $params),
            ]);
        }
    }

    private function getConfig($config)
    {
        if (is_string($config)) {
            return ['default' => $config];
        }

        if (!array_key_exists('default', $config)) {
            throw new InvalidArgumentException('config', 'Art direction image configuration MUST have a default value.');
        }

        return $config;
    }

}