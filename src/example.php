<?php
/**
 * FlexiBee WebHok destination
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */
require_once './init.php';

$historik = new \FlexiPeeHP\History\History(625,
    ['evidence' => 'cenik', 'mirror-dir' => $config['mirror-dir']]);

$change = $historik->getLastDataChange(1);

print_r($change);
