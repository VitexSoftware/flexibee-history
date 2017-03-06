<?php
/**
 * FlexiBee WebHook target
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */
require_once './init.php';

$hooker = new FlexiPeeHP\History\HookReciever();
$hooker->setMirrorDir($config['mirror-dir']);

$hooker->takeChanges(FlexiPeeHP\History\HookReciever::listen());
$hooker->processChanges();
