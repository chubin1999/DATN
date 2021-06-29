<?php
/*
 *  Copyright © CONVERT DIGITAL PTY LTD. All rights reserved.
 *  See COPYING.txt for license details
 */
declare(strict_types=1);

namespace Convert\Development\Plugin;

use Magento\User\Model\Backend\Config\ObserverConfig;

class AdminLogin extends AbstractPlugin
{
    /**
     * Don't force password change on development domains
     *
     * @param ObserverConfig $subject
     * @param bool           $result
     *
     * @return bool
     */
    public function afterIsPasswordChangeForced(
        ObserverConfig $subject,
        bool $result
    ) {
        // If in production mode, don't skip
        if ($this->configHelper->isProductionMode()) {
            return $result;
        }

        // Don't force password change on development domains
        if ($this->configHelper->isAllowedAdminUrl()) {
            return false;
        }

        return $result;
    }
}
