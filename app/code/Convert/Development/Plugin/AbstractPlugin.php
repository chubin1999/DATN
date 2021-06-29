<?php
/*
 *  Copyright © CONVERT DIGITAL PTY LTD. All rights reserved.
 *  See COPYING.txt for license details
 */
declare(strict_types=1);

namespace Convert\Development\Plugin;

use Convert\Development\Helper\ConfigHelper;

abstract class AbstractPlugin
{
    protected ConfigHelper $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }
}
