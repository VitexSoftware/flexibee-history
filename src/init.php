<?php
/**
 * FlexiBee History init
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */
include_once '../vendor/autoload.php';

/**
 * Check connection config
 *
 * @return boolean
 */
function isConnected()
{
    $connectStatus = false;
    $companer      = new \FlexiPeeHP\Company();
    $companies     = $companer->getFlexiData();
    if (isset($companies['company'])) {
        foreach ($companies['company'] as $company) {
            if ($company['dbNazev'] == constant('FLEXIBEE_COMPANY')) {
                $connectStatus = true;
            }
        }
    }
    return $connectStatus;
}

/**
 * Get Configuration values from json file $this->configFile and define UPPERCASE keys
 */
function loadConfig($configFile)
{
    $configuration = json_decode(file_get_contents($configFile), true);
    foreach ($configuration as $configKey => $configValue) {
        if ((strtoupper($configKey) == $configKey) && (!defined($configKey))) {
            define($configKey, $configValue);
        }
    }
    return $configuration;
}

/**
 * @global array $config FlexiBee History Configuration
 */
$config = loadConfig('localhost.json');


if (!isset($config['mirror-dir'])) {
    die('Mirror dir is not specified');
}

if (!isConnected()) {
    die('Not connected - Check configuration');
}

