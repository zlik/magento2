<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mustishipping checkout shipping
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping\Billing;

class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var \Magento\Checkout\Model\Type\Multishipping
     */
    protected $_multishipping;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Checkout\Model\Type\Multishipping $multishipping
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Checkout\Model\Type\Multishipping $multishipping,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_multishipping = $multishipping;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Get multishipping checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    public function getCheckout()
    {
        return $this->_multishipping;
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Retrieve virtual product edit url
     *
     * @return string
     */
    public function getVirtualProductEditUrl()
    {
        return $this->getUrl('*/cart');
    }

    /**
     * Retrieve virtual product collection array
     *
     * @return array
     */
    public function getVirtualQuoteItems()
    {
        $items = array();
        foreach ($this->getQuote()->getItemsCollection() as $_item) {
            if ($_item->getProduct()->getIsVirtual() && !$_item->getParentItemId()) {
                $items[] = $_item;
            }
        }
        return $items;
    }
}
