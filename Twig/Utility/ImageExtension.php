<?php


namespace Gie\EzProgressiveImageBundle\Twig\Utility;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'render_image',
                [ImageRuntime::class, 'render'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}