#!/usr/bin/php -q
<?php
/**
 * FlexiBee History
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */
require_once './init.php';


$historik = new \FlexiPeeHP\History\History(null,['mirror-dir'=>$config['mirror-dir']]);
foreach (array_keys(\FlexiPeeHP\EvidenceList::$name) as $evidence ){
    if($historik->setEvidence($evidence)){
        $historik->addStatusMessage(sprintf('evidence: '.$evidence),'success');
        $records = $historik->getColumnsFromFlexibee('id');
        if(count($records)){
            foreach ($records as $record){
                $change = ['@evidence'=>$evidence,'@operation'=>'create','id'=>$record['id'],'@in-version'=>0];
                $historik->saveChange($change);
            }
        }
    }
}
