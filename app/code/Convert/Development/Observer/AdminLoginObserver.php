<?php
/*
 *  Copyright © CONVERT DIGITAL PTY LTD. All rights reserved.
 *  See COPYING.txt for license details
 */
declare(strict_types=1);

namespace Convert\Development\Observer;

use Convert\Development\Helper\ConfigHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

class AdminLoginObserver implements ObserverInterface
{
    private ManagerInterface $messageManager;
    private ConfigHelper $configHelper;

    /**
     * @param ManagerInterface $messageManager
     * @param ConfigHelper     $configHelper
     */
    public function __construct(
        ManagerInterface $messageManager,
        ConfigHelper $configHelper
    ) {
        $this->messageManager = $messageManager;
        $this->configHelper   = $configHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        // Add message notification if we have admin users which shouldn't be on production
        if ($this->configHelper->isProductionMode()) {
            if ($this->configHelper->isRestrictedProductionUser()) {
                $username = $this->configHelper->getUsername();
                $this->messageManager->addNoticeMessage("User $username should not be enabled on production");
            }
        }
    }
}
