<?php
/*
 *  Copyright © CONVERT DIGITAL PTY LTD. All rights reserved.
 *  See COPYING.txt for license details
 */
declare(strict_types=1);

namespace Convert\Development\Plugin;

use Magento\TwoFactorAuth\Model\TfaSession;

class TfaGranted extends AbstractPlugin
{
    /**
     * Returns true for 2FA authentication granted if the user is an approved user, the domain is a development domain
     * or the site is in developer mode
     *
     * @param TfaSession $subject
     * @param            $result
     *
     * @return bool
     */
    public function afterIsGranted(TfaSession $subject, $result): bool
    {
        // Skip auth if allowed user, even in production mode
        if ($this->configHelper->isAllowedUser()) {
            return true;
        }

        // If in production mode, don't skip based on domain
        if ($this->configHelper->isProductionMode()) {
            return $result;
        }

        // Skip auth if Magento is on a development domain
        if ($this->configHelper->isAllowedAdminUrl()) {
            return true;
        }

        return $result;
    }
}
