<?php

namespace Gie\EzProgressiveImageBundle\Imagine\Filter;

use eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\FilterConfiguration as EzFilterConfiguration;

class FilterConfiguration extends EzFilterConfiguration
{
    const POST_PROCESSORS = [
        "post_processors" => [
            "jpegoptim" => [
                "strip_all" => true,
                "max" => 85,
                "progressive" => true,
            ],
            /*"optipng" => [
                "strip_all" => true,
                "level" => 5,
            ]*/
        ]
    ];

    const HI_POST_PROCESSORS = [
        "post_processors" => [
            "jpegoptim" => [
                "max" => 100,
            ],
        ]
    ];

    const LOW_POST_PROCESSORS = [
        "post_processors" => [
            "jpegoptim" => [
                "strip_all" => true,
                "max" => 60,
            ],
        ]
    ];

    public function get($filter)
    {
        if (preg_match('/^(?P<alias>[\w-]+)\.((?P<ratio>\d)x|placeholder)$/', $filter, $match)) {
            $baseFilter = parent::get($match['alias']);

            if (!isset($match['ratio'])) {
                return array_replace_recursive(parent::get('ez_progressive_placeholder'), ['reference' => $match['alias']]);
            }

           foreach ($baseFilter['filters'] as $name => $params) {
                if (isset($baseFilter['filters'][$name]['size'])) {
                    $baseFilter['filters'][$name]['size'] = $this->applyRatio($match['ratio'], $baseFilter['filters'][$name]['size']);
                } else {
                    $baseFilter['filters'][$name] = $this->applyRatio((int)$match['ratio'], $baseFilter['filters'][$name]);
                }
            }

            return array_replace_recursive($baseFilter, self::POST_PROCESSORS, self::LOW_POST_PROCESSORS);
        }

        return array_replace_recursive(parent::get($filter),self::POST_PROCESSORS);

    }

    private function applyRatio(int $ratio, array $params)
    {
        return array_map(function ($value) use ($ratio) {
            return $value * $ratio;
        }, $params);
    }


}