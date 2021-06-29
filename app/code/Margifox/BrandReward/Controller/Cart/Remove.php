<?php

namespace Margifox\BrandReward\Controller\Cart;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

class Remove implements \Magento\Framework\App\ActionInterface, HttpPostActionInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Reward\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Reward\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Magento\Reward\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        RequestInterface $request
    ) {
        $this->session = $session;
        $this->helper = $helper;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $redirect = $this->redirectFactory->create();
        if (!$this->helper->isEnabledOnFront() || !$this->helper->getHasRates()) {
            return $redirect->setPath('customer/account/');
        }

        $brandId = $this->request->getParam('brand_id');
        $rewardType = $this->request->getParam('type');

        if (!$brandId) {
            throw new \RuntimeException('Brand Id is empty.');
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->session->getQuote();

        if ($quote->getUseRewardPoints()) {
            $brandRewardIds = $quote->getData('brand_reward_ids');
            $brandRewardIds = $brandRewardIds ? unserialize($brandRewardIds) : [];
            if (($key = array_search($brandId, $brandRewardIds[$rewardType])) !== false) {
                unset($brandRewardIds[$rewardType][$key]);
            }
            foreach ($brandRewardIds as $key => $brandRewardId) {
                if (empty($brandRewardId)) {
                    unset($brandRewardIds[$key]);
                }
            }

            $quote->setData('brand_reward_ids', serialize($brandRewardIds));
            $quote->setUseRewardPoints((bool)$brandRewardIds)->collectTotals()->save();
            $this->messageManager->addSuccessMessage(__('You removed the reward points from this order.'));
        } else {
            $this->messageManager->addErrorMessage(__('Reward points will not be used in this order.'));
        }

        $referer = $this->request->getParam('_referer');
        if ($referer === 'payment') {
            return $redirect->setPath('checkout', ['_fragment' => 'payment']);
        }

        return $redirect->setPath('checkout/cart');
    }
}
