<?php

/**
 * Meta Tags Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\MetaTags\Helper;

use Magento\Store\Model\ScopeInterface;


class Product extends AbstractHelper
{
    const XML_PATH_IS_ENABLED = 'catalog/konstanchuk_metatags_product/active';
    const XML_PATH_REPLACE_TYPE = 'catalog/konstanchuk_metatags_product/replace_type';
    const XML_PATH_META_TITLE = 'catalog/konstanchuk_metatags_product/title';
    const XML_PATH_META_KEYWORDS = 'catalog/konstanchuk_metatags_product/keywords';
    const XML_PATH_META_DESCRIPTION = 'catalog/konstanchuk_metatags_product/description';
    const XML_PATH_ADDITIONAL_META_TAGS = 'catalog/konstanchuk_metatags_product/additional_meta_tags';

    public function isEnabled($store = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_IS_ENABLED, $store);
    }

    public function getReplaceType($store = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_REPLACE_TYPE, $store);
    }

    public function getTitle($store = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_META_TITLE, $store);
    }

    public function getKeywords($store = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_META_KEYWORDS, $store);
    }

    public function getDescription($store = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(static::XML_PATH_META_DESCRIPTION, $store);
    }

    public function getAdditionalMetaTags($store = ScopeInterface::SCOPE_STORE)
    {
        $configTable = $this->scopeConfig->getValue(static::XML_PATH_ADDITIONAL_META_TAGS, $store);
        if ($configTable) {
            return $this->unserializeConfigTable($configTable, ['name', 'content']);
        }
        return [];
    }
}
