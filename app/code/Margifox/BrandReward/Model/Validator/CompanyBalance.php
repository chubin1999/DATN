<?php

namespace Margifox\BrandReward\Model\Validator;

use Magento\Framework\Exception\PaymentException;
use Magento\Quote\Model\Quote;

class CompanyBalance
{
    /**
     * @var \Margifox\BrandReward\Service\GetCurrentCustomerCompany
     */
    private $getCurrentCustomerCompany;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Margifox\BrandReward\Service\GetQuoteItemsPerBrands
     */
    private $quoteItemsPerBrands;

    /**
     * @param \Margifox\BrandReward\Service\GetCurrentCustomerCompany $getCurrentCustomerCompany
     * @param \Magento\Checkout\Model\Session $session
     * @param \Margifox\BrandReward\Service\GetQuoteItemsPerBrands $quoteItemsPerBrands
     */
    public function __construct(
        \Margifox\BrandReward\Service\GetCurrentCustomerCompany $getCurrentCustomerCompany,
        \Magento\Checkout\Model\Session $session,
        \Margifox\BrandReward\Service\GetQuoteItemsPerBrands $quoteItemsPerBrands
    ) {
        $this->getCurrentCustomerCompany = $getCurrentCustomerCompany;
        $this->session = $session;
        $this->quoteItemsPerBrands = $quoteItemsPerBrands;
    }

    /**
     * @param Quote $quote
     * @throws PaymentException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(Quote $quote): void
    {
        if ($quote->getRewardPointsBalance() <= 0) {
            return;
        }

        if (!$this->getCurrentCustomerCompany->execute()) {
            throw new \RuntimeException(__('Can\'t find company for customer.'));
        }

        $quotePricePerBrand = $this->quoteItemsPerBrands->execute($quote);

        // TODO: Split logic for loyalty points and allocation points balance in some way
        //  for the cases when the first customer added to cart products with brand rewards points
        //  and the second customer applies brand rewards points and creates an order
        //  in this case the first customer cart data is invalid

        foreach ($quotePricePerBrand as $item) {
            $totalRewardPoints = $item->getSpendingLevel()->getLoyaltyPointsBalance() + $item->getSpendingLevel()->getAllocationPointsBalance();
            if ($quote->getRewardPointsBalance() > $totalRewardPoints) {
                $this->session->setUpdateSection('payment-method');
                $this->session->setGotoSection('payment');
                throw new PaymentException(__('You don\'t have enough reward points to pay for this purchase.'));
            }
        }
    }
}
