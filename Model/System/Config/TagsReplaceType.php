<?php

/**
 * Meta Tags Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\MetaTags\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;


class TagsReplaceType implements ArrayInterface
{
    const ALWAYS_REPLACE  = 1;
    const REPLACE_IF_NOT_EXISTS = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ALWAYS_REPLACE => __('Always replace'),
            self::REPLACE_IF_NOT_EXISTS => __('Replace if not exists')
        ];

        return $options;
    }
}