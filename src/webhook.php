<?php
/**
 * FlexiBee WebHok destination
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */
require_once './init.php';

$hooker = new FlexiPeeHP\History\HookReciever();
$hooker->setMirrorDir($config['mirror-dir']);

$hooker->takeChanges(FlexiPeeHP\History\HookReciever::listen());
//$hooker->takeChanges(json_decode(file_get_contents('../testing/changes/flexibee-changes-127.0.0.1_1488753322.json'),TRUE));
$hooker->processChanges();
