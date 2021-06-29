<?php

// Define Margifox domains
const MARGIFOX_DOMAINS = [
    // Production
    'margifox.com.au',
    'www.margifox.com.au',
    'mcprod.margifox.com.au',
    'master-7rqtwti-aszol7oknl6fm.ap-3.magentosite.cloud',

    // Staging
    'mcstaging.margifox.com.au',
    'staging-5em2ouy-aszol7oknl6fm.ap-3.magentosite.cloud'
];

/**
 * @param $host
 * @return bool
 */
function isHttpHost($host)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return $_SERVER['HTTP_HOST'] === $host;
}

/**
 * @param  array  $hosts
 * @return bool
 */
function isSite(array $hosts): bool
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return in_array($_SERVER['HTTP_HOST'], $hosts);
}


// Modify if any additional domains are used in future
//if ($this->isSite(MARGIFOX_DOMAINS)) {
//    $_SERVER["MAGE_RUN_CODE"] = "default";
//}

$_SERVER["MAGE_RUN_CODE"] = "default";
$_SERVER["MAGE_RUN_TYPE"] = "store";
