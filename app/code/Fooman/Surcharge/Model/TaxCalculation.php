<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\TaxDetailsInterfaceFactory;
use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Calculation\AbstractCalculator;
use Magento\Tax\Model\Calculation\CalculatorFactory;
use Magento\Tax\Model\Config as TaxConfig;

class TaxCalculation extends \Magento\Tax\Model\TaxCalculation
{
    private $adjustNextTaxCalc;

    public function __construct(
        Calculation $calculation,
        CalculatorFactory $calculatorFactory,
        TaxConfig $config,
        TaxDetailsInterfaceFactory $taxDetailsDataObjectFactory,
        TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory,
        StoreManagerInterface $storeManager,
        TaxClassManagementInterface $taxClassManagement,
        DataObjectHelper $dataObjectHelper,
        AdjustNextTaxCalc $adjustNextTaxCalc
    ) {
        $this->adjustNextTaxCalc = $adjustNextTaxCalc;
        parent::__construct(
            $calculation,
            $calculatorFactory,
            $config,
            $taxDetailsDataObjectFactory,
            $taxDetailsItemDataObjectFactory,
            $storeManager,
            $taxClassManagement,
            $dataObjectHelper
        );
    }

    /**
     * Wrap calculation of surcharge items to fix missing tax on negative items
     *
     * Magento zeroes out tax amounts if they are negative during the discount calculation when Apply Customer Tax
     * is set to After Discount
     * @see https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Tax/Model/Calculation/AbstractAggregateCalculator.php#L52
     *
     * @param QuoteDetailsItemInterface $item
     * @param AbstractCalculator        $calculator
     * @param bool                      $round
     *
     * @return \Magento\Tax\Api\Data\TaxDetailsItemInterface
     */
    protected function processItem(
        QuoteDetailsItemInterface $item,
        AbstractCalculator $calculator,
        $round = true
    ) {
        if (!$this->requiresIntervention($item)) {
            return parent::processItem($item, $calculator, $round);
        }
        $this->adjustNextTaxCalc->startAdjusting();
        $result = parent::processItem($item, $calculator, $round);
        $this->adjustNextTaxCalc->stopAdjusting();
        return $result;
    }

    public function requiresIntervention(QuoteDetailsItemInterface $item)
    {
        if ($item->getType() !== 'fooman_surcharge') {
            return false;
        }
        if ($item->getUnitPrice() >= 0) {
            return false;
        }
        return $this->config->applyTaxAfterDiscount();
    }
}
