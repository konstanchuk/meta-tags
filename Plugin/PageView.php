<?php

/**
 * Meta Tags Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\MetaTags\Plugin;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Konstanchuk\MetaTags\Model\TagsPrepare;


abstract class PageView
{
    /** @var  ResponseInterface */
    protected $_response;

    /** @var  Registry */
    protected $_registy;

    /** @var  TagsPrepare */
    protected $_tagPrepare;

    public function __construct(
        ResponseInterface $response,
        Registry $registry,
        TagsPrepare $tagsPrepare
    )
    {
        $this->_response = $response;
        $this->_registy = $registry;
        $this->_tagPrepare = $tagsPrepare;
    }
}