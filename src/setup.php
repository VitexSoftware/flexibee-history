<?php
/**
 * FlexiBee History
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */
require_once './init.php';

$oPage     = new \Ease\TWB\WebPage('FlexiPeeHP');
$container = $oPage->addItem(new \Ease\TWB\Container(new \Ease\Html\H1Tag(_('FlexiBee History Init'))));


$changer = new FlexiPeeHP\Changes();
$changer->enable();

if ($changer->getStatus()) {
    $changer->addStatusMessage(_('ChangesApi Enabled'), 'success');
    $hooker = new \FlexiPeeHP\Hooks();

    $webHookUrl = str_replace(basename(__FILE__), 'webhook.php',
        \Ease\Page::phpSelf());

    $hookResult = $hooker->register($webHookUrl, 'json');
    if ($hookResult) {
        $hooker->addStatusMessage(sprintf(_('Hook %s was registered'),
                $webHookUrl), 'success');
    } else {
        $hooker->addStatusMessage(sprintf(_('Hook %s not registered'),
                $webHookUrl), 'warning');
    }
} else {
    $changer->addStatusMessage(_('ChangesApi Disables', 'warning'));
}

$mirrordir  = $config['mirror-dir'];
if (file_exists($mirrordir)) {
    system('rm -rf '.$mirrordir);
}
mkdir($mirrordir);

mkdir($mirrordir.$changer->company);
$git = new FlexiPeeHP\History\GitStorage($mirrordir);
$oPage->addStatusMessage(implode('\n', $git->init()));
$git->config('user.name', $config['EASE_APPNAME']);
$git->config('user.eamail', $changer->user.'@'.$changer->url);

file_put_contents($mirrordir.'/'.$config['EASE_APPNAME'],
    $config['EASE_APPNAME']);
$git->add('.');
$git->commit('Git Init');


$container->addItem($oPage->getStatusMessagesAsHtml());


$oPage->draw();
