<?php

/**
 * Meta Tags Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\MetaTags\Plugin\Magento\Catalog\Controller\Product;

use Konstanchuk\MetaTags\Helper\Product as ProductHelper;
use Konstanchuk\MetaTags\Model\TagsPrepare;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page as ResultPage;
use Konstanchuk\MetaTags\Plugin\PageView;


class View extends PageView
{
    /** @var  ProductHelper */
    protected $_helper;

    public function __construct(
        ResponseInterface $response,
        Registry $registry,
        TagsPrepare $tagsPrepare,
        ProductHelper $helper
    )
    {
        parent::__construct($response, $registry, $tagsPrepare);
        $this->_helper = $helper;
    }

    public function afterExecute($subject, $result)
    {
        if ($this->_helper->isEnabled()
            && $result instanceof ResultPage
            && $this->_response->getStatusCode() == 200
        ) {
            $product = $this->_registy->registry('current_product');
            if ($product) {
                $this->_tagPrepare->setProductMetaTags($product, $result);
            }
        }
        return $result;
    }
}