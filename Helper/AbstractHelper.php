<?php

/**
 * Meta Tags Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\MetaTags\Helper;

use Magento\Framework\App\Helper\AbstractHelper as CoreAbstractHelper;
use Magento\Store\Model\ScopeInterface;


abstract class AbstractHelper extends CoreAbstractHelper
{
    public abstract function isEnabled($store = ScopeInterface::SCOPE_STORE);

    public abstract function getReplaceType($store = ScopeInterface::SCOPE_STORE);

    public abstract function getTitle($store = ScopeInterface::SCOPE_STORE);

    public abstract function getKeywords($store = ScopeInterface::SCOPE_STORE);

    public abstract function getDescription($store = ScopeInterface::SCOPE_STORE);

    public function getAdditionalMetaTags($store = ScopeInterface::SCOPE_STORE)
    {
        return [];
    }

    protected function unserializeConfigTable($configTable, array $columns)
    {
        $tableConfigResults = unserialize($configTable);
        $result = [];
        if (is_array($tableConfigResults)) {
            foreach ($tableConfigResults as $item) {
                $row = [];
                foreach ($columns as $column) {
                    $row[$column] = isset($item[$column]) ? $item[$column] : null;
                }
                $result[] = $row;
            }
        }
        return $result;
    }
}
