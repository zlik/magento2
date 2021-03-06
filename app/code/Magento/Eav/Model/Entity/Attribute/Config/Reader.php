<?php
/**
 * Attribute configuration reader
 *
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
 * @copyright Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Eav\Model\Entity\Attribute\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * Xml merging attributes
     *
     * @var array
     */
    protected $_idAttributes = array(
        'config/entity' => 'type',
        'config/entity/attribute' => 'code',
        'config/entity/attribute/field' => 'code'
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Eav\Model\Entity\Attribute\Config\Converter $converter
     * @param \Magento\Eav\Model\Entity\Attribute\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Eav\Model\Entity\Attribute\Config\Converter $converter,
        \Magento\Eav\Model\Entity\Attribute\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState
    ) {
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, 'eav_attributes.xml', array());
    }
}
