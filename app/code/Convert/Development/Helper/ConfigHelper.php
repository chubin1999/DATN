<?php
/*
 *  Copyright © CONVERT DIGITAL PTY LTD. All rights reserved.
 *  See COPYING.txt for license details
 */
declare(strict_types=1);

namespace Convert\Development\Helper;

use Laminas\Uri\Uri;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\State;

class ConfigHelper
{
    private UrlInterface $backendUrl;
    private Uri $uri;
    private State $state;
    private Session $authSession;
    private array $allowedDomains;
    private array $allowedUsers;
    private array $restrictedProductionUsers;

    /**
     * @param UrlInterface $backendUrl
     * @param Uri          $uri
     * @param State        $state
     * @param Session      $authSession
     * @param array        $allowedDomains
     * @param array        $allowedUsers
     * @param array        $restrictedProductionUsers
     */
    public function __construct(
        UrlInterface $backendUrl,
        Uri $uri,
        State $state,
        Session $authSession,
        array $allowedDomains = [],
        array $allowedUsers = [],
        array $restrictedProductionUsers = []
    ) {
        $this->backendUrl                = $backendUrl;
        $this->uri                       = $uri;
        $this->state                     = $state;
        $this->authSession               = $authSession;
        $this->allowedDomains            = $allowedDomains;
        $this->allowedUsers              = $allowedUsers;
        $this->restrictedProductionUsers = $restrictedProductionUsers;
    }

    public function getUsername(): string
    {
        return $this->authSession->getUser()->getUserName();
    }

    /**
     * Is the user an allowed user
     *
     * @return bool
     */
    public function isAllowedUser(): bool
    {
        return in_array($this->getUsername(), $this->allowedUsers);
    }

    /**
     * Is the user in the restricted production users list
     *
     * @return bool
     */
    public function isRestrictedProductionUser(): bool
    {
        return in_array($this->getUsername(), $this->restrictedProductionUsers);
    }

    /**
     * Is Magento in production mode
     *
     * @return bool
     */
    public function isProductionMode(): bool
    {
        return $this->state->getMode() == State::MODE_PRODUCTION;
    }

    /**
     * Is the admin URL in the list of approved domains
     *
     * @return bool
     */
    public function isAllowedAdminUrl(): bool
    {
        return $this->isAllowedDomain($this->backendUrl->getRouteUrl('adminhtml'));
    }

    /**
     * Is the domain within the list of approved domains
     *
     * @param string $domain
     *
     * @return bool
     */
    private function isAllowedDomain(string $domain): bool
    {
        foreach ($this->allowedDomains as $allowedDomain) {
            if (strpos($domain, $allowedDomain) !== false) {
                return true;
            }
        }
        return false;
    }
}
