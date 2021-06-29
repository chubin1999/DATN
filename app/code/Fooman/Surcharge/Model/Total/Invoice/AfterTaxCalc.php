<?php
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model\Total\Invoice;

class AfterTaxCalc extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{

    /**
     * If the Invoice is not last Magento does not automatically
     * get the correct taxable amounts, correct for it here
     *
     * @param  \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        if (!$invoice->isLast()) {
            $invoiceTotals = 0;
            $baseInvoiceTotals = 0;
            $foomanTotals = $this->getTotalsFromExtAttr($invoice);

            if (empty($foomanTotals)) {
                return $this;
            }
            foreach ($foomanTotals as $total) {
                $invoiceTotals += $total->getTaxAmount();
                $baseInvoiceTotals += $total->getBaseTaxAmount();
            }

            $order = $invoice->getOrder();
            $allowedTax = $order->getTaxAmount() - $order->getTaxInvoiced();
            if ($invoice->getTaxAmount() + $invoiceTotals <= $allowedTax) {
                $this->adjustInvoiceTotals($invoice, $invoiceTotals, $baseInvoiceTotals);
            } elseif ($this->hasDummy($invoice)) {
                $this->adjustInvoiceTotals($invoice, $invoiceTotals, $baseInvoiceTotals);
            }
        }

        return $this;
    }

    /**
     * On carts which have a configurable product the invoice gets into the
     * !isLast() section in
     * @see \Magento\Sales\Model\Order\Invoice\Total\Tax
     * and can lead to surcharge taxes missing on invoice tax and grand total
     * (observed on 2.2.7)
     * https://github.com/magento/magento2/issues/22283
     */
    private function hasDummy($invoice)
    {
        foreach ($invoice->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                return true;
            }
        }
        return false;
    }

    private function getTotalsFromExtAttr(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $returnTotals = [];
        $extensionAttributes = $invoice->getExtensionAttributes();
        if (!$extensionAttributes) {
            return $returnTotals;
        }
        $foomanTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanTotalGroup) {
            return $returnTotals;
        }
        $orderTotals = $foomanTotalGroup->getItems();
        if (empty($orderTotals)) {
            return $returnTotals;
        }

        foreach ($orderTotals as $orderTotal) {
            if ($orderTotal->getCode() == \Fooman\Surcharge\Model\Surcharge::CODE) {
                $returnTotals[] = $orderTotal;
            }
        }

        return $returnTotals;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param $invoiceTotals
     * @param $baseInvoiceTotals
     */
    public function adjustInvoiceTotals(\Magento\Sales\Model\Order\Invoice $invoice, $invoiceTotals, $baseInvoiceTotals)
    {
        $invoice->setTaxAmount($invoice->getTaxAmount() + $invoiceTotals);
        $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $baseInvoiceTotals);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoiceTotals);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseInvoiceTotals);
    }
}
