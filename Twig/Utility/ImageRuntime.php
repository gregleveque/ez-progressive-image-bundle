<?php


namespace Gie\EzProgressiveImageBundle\Twig\Utility;


use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\FieldType\ImageAsset\AssetMapper;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class ImageRuntime implements RuntimeExtensionInterface
{
    /** @var string  */
    const DEFAULT_PARAMS = [
        'class' => '',
        'alt' => '',
        'parent-fit' => 'cover',
        'object-fit' => 'cover',
        'caption' => '',
        'captionFieldIdentifier' => 'caption',
    ];

    /** @var \Twig\Environment */
    private $twig;

    /** @var \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper */
    private $mapper;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * ImageRuntime constructor.
     * @param \Twig\Environment $twig
     * @param \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper $mapper\
     */
    public function __construct(Environment $twig, AssetMapper $mapper, ContentService $contentService)
    {
        $this->twig = $twig;
        $this->mapper = $mapper;
        $this->contentService = $contentService;
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

        return $this->twig->render('@EzProgressiveImage/image.html.twig', [
            'content' => $content,
            'identifier' => $fieldIdentifier,
            'config' => $this->getConfig($config),
            'parameters' => array_replace_recursive(self::DEFAULT_PARAMS, ['alt' => $alt],  $params),
        ]);
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