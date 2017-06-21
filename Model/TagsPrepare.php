<?php

/**
 * Meta Tags Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\MetaTags\Model;

use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Model\Category as ModelCategory;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;
use Konstanchuk\MetaTags\Helper\Product as ProductHelper;
use Konstanchuk\MetaTags\Helper\Category as CategoryHelper;
use Konstanchuk\MetaTags\Helper\AbstractHelper;
use Konstanchuk\MetaTags\Model\System\Config\TagsReplaceType as ReplaceOptions;


class TagsPrepare
{
    /** @var  ProductHelper */
    protected $_productHelper;

    /** @var  CategoryHelper */
    protected $_categoryHelper;

    /** @var  LoggerInterface */
    protected $_logger;

    /** @var  EventManager */
    protected $_eventManager;

    protected $replaceFunctions;

    public function __construct(
        EventManager $eventManager,
        LoggerInterface $logger,
        ProductHelper $productHelper,
        CategoryHelper $categoryHelper
    )
    {
        $this->_productHelper = $productHelper;
        $this->_categoryHelper = $categoryHelper;
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;

        $this->replaceFunctions = [
            'uppercase' => function ($key, DataObject $object) {
                $value = (string)$object->getData($key);
                return mb_strtoupper($value);
            },
            'lowercase' => function ($key, DataObject $object) {
                $value = (string)$object->getData($key);
                return mb_strtolower($value);
            },
            'function' => function ($key, DataObject $object) use ($logger) {
                try {
                    $reflectionMethod = new \ReflectionMethod($object, $key);
                    if ($reflectionMethod->getNumberOfRequiredParameters() === 0) {
                        return (string)$reflectionMethod->invoke($object);
                    } else {
                        throw new \ReflectionException(__('the called function "$1" cannot have parameters', $key));
                    }
                } catch (\ReflectionException $e) {
                    $logger->critical(__('MetaTags reflection error: $1', $e->getMessage()));
                } catch (\Exception $e) {
                    $logger->critical(__('MetaTags exceptions: $1', $e->getMessage()));
                }
                return '';
            },
        ];

        $this->_eventManager->dispatch('konstanchuk_metatags_init_replace_functions', [
            'replace_functions' => &$this->replaceFunctions
        ]);
    }

    public function setProductMetaTags(ModelProduct $product, ResultPage $page)
    {
        $this->setMetaTags($product, $page, $this->_productHelper);
    }

    public function setCategoryMetaTags(ModelCategory $category, ResultPage $page)
    {
        $this->setMetaTags($category, $page, $this->_categoryHelper);
    }

    public function setOtherPageMetaTags(DataObject $model, ResultPage $page, AbstractHelper $helper)
    {
        $this->setMetaTags($model, $page, $helper);
    }

    protected function getMetaTagValue(DataObject $object, $configValue)
    {
        if (!preg_match_all('/\{\{(.*)\}\}/U', $configValue, $matches)) {
            return false;
        }
        if (!isset($matches[1])) {
            return false;
        }

        $attributes = [];
        foreach ($matches[1] as $keyName) {
            $explodedKey = array_filter(explode(':', $keyName, 2));
            try {
                if (!isset($explodedKey[1])) {
                    $value = $object->getData($keyName);
                } else {
                    $function = strtolower($explodedKey[0]);
                    $attribute = $explodedKey[1];
                    if (isset($this->replaceFunctions[$function])
                        && $this->replaceFunctions[$function] instanceof \Closure
                    ) {
                        $value = $this->replaceFunctions[$function]($attribute, $object);
                    } else {
                        $value = '';
                    }
                }
                $attributes['{{' . $keyName . '}}'] = (string)$value;
            } catch (\Exception $e) {
                $this->_logger->critical(__('MetaTags replace error: $1', $e->getMessage()));
            }
        }
        return strtr($configValue, $attributes);
    }

    protected function setMetaTags(DataObject $object, ResultPage $page, AbstractHelper $helper)
    {
        $alwaysReplace = $helper->getReplaceType() == ReplaceOptions::ALWAYS_REPLACE;
        $pageConfig = $page->getConfig();
        $metaData = $pageConfig->getMetadata();

        if (empty($metaData['description']) || $alwaysReplace) {
            $configValue = $helper->getDescription();
            if (!empty($configValue)) {
                $pageConfig->setMetadata('description', $this->getMetaTagValue($object, $configValue));
            }
        }

        if (empty($metaData['keywords']) || $alwaysReplace) {
            $configValue = $helper->getKeywords();
            if (!empty($configValue)) {
                $pageConfig->setMetadata('keywords', $this->getMetaTagValue($object, $configValue));
            }
        }

        $title = $pageConfig->getTitle();
        $titleValue = $title->get();
        if (empty($titleValue) || $alwaysReplace) {
            $configValue = $helper->getTitle();
            if (!empty($configValue)) {
                $title->set($this->getMetaTagValue($object, $configValue));
            }
        }

        $additionalTags = $helper->getAdditionalMetaTags();
        foreach ($additionalTags as $item) {
            if ($alwaysReplace || (isset($metaData[$item['name']]) && empty($metaData[$item['name']]))) {
                $pageConfig->setMetadata($item['name'], $this->getMetaTagValue($object, $item['content']));
            }
        }
    }
}
