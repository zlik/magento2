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
 * @package     Magento_Adminhtml
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml group price item abstract renderer
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group;

abstract class AbstractGroup
    extends \Magento\Adminhtml\Block\Widget
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Form element instance
     *
     * @var \Magento\Data\Form\Element\AbstractElement
     */
    protected $_element;

    /**
     * Customer groups cache
     *
     * @var array
     */
    protected $_customerGroups;

    /**
     * Websites cache
     *
     * @var array
     */
    protected $_websites;

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $_groupFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_groupFactory = $groupFactory;
        $this->_directoryHelper = $directoryHelper;
        $this->_catalogData = $catalogData;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Retrieve current product instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Render HTML
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Set form element instance
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup
     */
    public function setElement(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve form element instance
     *
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Prepare group price values
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $data = $this->getElement()->getValue();

        if (is_array($data)) {
            $values = $this->_sortValues($data);
        }

        foreach ($values as &$value) {
            $value['readonly'] = ($value['website_id'] == 0)
                && $this->isShowWebsiteColumn()
                && !$this->isAllowChangeWebsite();
        }

        return $values;
    }

    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        return $data;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @param int|null $groupId  return name by customer group id
     * @return array|string
     */
    public function getCustomerGroups($groupId = null)
    {
        if ($this->_customerGroups === null) {
            if (!$this->_catalogData->isModuleEnabled('Magento_Customer')) {
                return array();
            }
            $collection = $this->_groupFactory->create()->getCollection();
            $this->_customerGroups = $this->_getInitialCustomerGroups();

            foreach ($collection as $item) {
                /** @var $item \Magento\Customer\Model\Group */
                $this->_customerGroups[$item->getId()] = $item->getCustomerGroupCode();
            }
        }

        if ($groupId !== null) {
            return isset($this->_customerGroups[$groupId]) ? $this->_customerGroups[$groupId] : array();
        }

        return $this->_customerGroups;
    }

    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    protected function _getInitialCustomerGroups()
    {
        return array();
    }

    /**
     * Retrieve number of websites
     *
     * @return int
     */
    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    /**
     * Show website column and switcher for group price table
     *
     * @return bool
     */
    public function isMultiWebsites()
    {
        return !$this->_storeManager->isSingleStoreMode();
    }

    /**
     * Retrieve allowed for edit websites
     *
     * @return array
     */
    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }

        $this->_websites = array(
            0 => array(
                'name' => __('All Websites'),
                'currency' => $this->_directoryHelper->getBaseCurrencyCode()
            )
        );

        if (!$this->isScopeGlobal() && $this->getProduct()->getStoreId()) {
            /** @var $website \Magento\Core\Model\Website */
            $website = $this->_storeManager->getStore($this->getProduct()->getStoreId())->getWebsite();

            $this->_websites[$website->getId()] = array(
                'name' => $website->getName(),
                'currency' => $website->getBaseCurrencyCode()
            );
        } elseif (!$this->isScopeGlobal()) {
            $websites = $this->_storeManager->getWebsites(false);
            $productWebsiteIds  = $this->getProduct()->getWebsiteIds();
            foreach ($websites as $website) {
                /** @var $website \Magento\Core\Model\Website */
                if (!in_array($website->getId(), $productWebsiteIds)) {
                    continue;
                }
                $this->_websites[$website->getId()] = array(
                    'name' => $website->getName(),
                    'currency' => $website->getBaseCurrencyCode()
                );
            }
        }

        return $this->_websites;
    }

    /**
     * Retrieve default value for customer group
     *
     * @return int
     */
    public function getDefaultCustomerGroup()
    {
        return \Magento\Customer\Model\Group::CUST_GROUP_ALL;
    }

    /**
     * Retrieve default value for website
     *
     * @return int
     */
    public function getDefaultWebsite()
    {
        if ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()) {
            return $this->_storeManager->getStore($this->getProduct()->getStoreId())->getWebsiteId();
        }
        return 0;
    }

    /**
     * Retrieve 'add group price item' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Retrieve customized price column header
     *
     * @param string $default
     * @return string
     */
    public function getPriceColumnHeader($default)
    {
        if ($this->hasData('price_column_header')) {
            return $this->getData('price_column_header');
        } else {
            return $default;
        }
    }

    /**
     * Retrieve customized price column header
     *
     * @param string $default
     * @return string
     */
    public function getPriceValidation($default)
    {
        if ($this->hasData('price_validation')) {
            return $this->getData('price_validation');
        } else {
            return $default;
        }
    }

    /**
     * Retrieve Group Price entity attribute
     *
     * @return \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    public function getAttribute()
    {
        return $this->getElement()->getEntityAttribute();
    }

    /**
     * Check group price attribute scope is global
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getAttribute()->isScopeGlobal();
    }

    /**
     * Show group prices grid website column
     *
     * @return bool
     */
    public function isShowWebsiteColumn()
    {
        if ($this->isScopeGlobal() || $this->_storeManager->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

    /**
     * Check is allow change website value for combination
     *
     * @return bool
     */
    public function isAllowChangeWebsite()
    {
        if (!$this->isShowWebsiteColumn() || $this->getProduct()->getStoreId()) {
            return false;
        }
        return true;
    }
}
