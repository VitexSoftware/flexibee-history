<?php

namespace FlexiPeeHP\History;

/**
 * FlexiBee History
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */
class History extends \FlexiPeeHP\FlexiBeeRO
{
    /**
     * Where store changes
     * @var string path
     */
    public $mirrorDir = '/var/tmp/flexibee/history';

    /**
     * GitClass
     * @var \PHPGit\Git
     */
    public $git = null;

    public function setMirrorDir($mirrorDir)
    {
        $this->git       = new GitStorage($mirrorDir);
        $this->mirrorDir = $mirrorDir;
    }

    public function saveChange($change)
    {
        $operation = $change['@operation'];
        $evidence  = $change['@evidence'];
        $id        = intval($change['id']);

        $changeFile = $this->getChangeFile($change);
        if (!file_exists(dirname($changeFile))) {
            mkdir(dirname($changeFile), 0777, TRUE);
        }
        if (!file_exists($changeFile)) {
            $operation = 'create';
        }

        switch ($operation) {
            case 'delete':
                $this->git->rm($changeFile);
                break;
            case 'create':
            default:
                $this->setEvidence($evidence);
                $this->loadFromFlexiBee($id);
                $changeToSave = json_encode($this->getData(), JSON_PRETTY_PRINT);
                file_put_contents($changeFile, $changeToSave);
                if ($operation == 'create') {
                    $this->git->add(dirname($changeFile));
                }
                break;
        }
        return $this->gitCommit($change);
    }

    public function gitCommit($change)
    {
        $id            = intval($change['id']);
        $inVersion     = intval($change['@in-version']);
        $evidence      = $change['@evidence'];
        $operation     = $change['@operation'];
        $changeMessage = $inVersion.' '.$evidence.' '.$operation.' #'.$id;
        $this->addStatusMessage($changeMessage);
        return $this->git->commit($changeMessage);
    }

    public function getChangeFile($change)
    {
        $id         = intval($change['id']);
        $changeFile = $this->getChangeDir($change).$id.'.'.$this->format;
        return $changeFile;
    }

    public function getChangeDir($change)
    {
        $evidence = $change['@evidence'];
        return $this->mirrorDir.$this->company.'/'.$evidence.'/';
    }
}
