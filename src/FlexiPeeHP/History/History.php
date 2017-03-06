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

    /**
     * SetUp Object to be ready for connect
     *
     * @param array $options Object Options (mirror-dir,company,url,user,password,evidence,
     *                                       prefix,debug)
     */
    public function setUp($options = [])
    {
        parent::setUp($options);
        if (isset($options['mirror-dir'])) {
            $this->setMirrorDir($options['mirror-dir']);
        }
    }

    /**
     * Set Git Repo Destination
     *
     * @param string $mirrorDir path
     */
    public function setMirrorDir($mirrorDir)
    {
        $this->git       = new GitStorage($mirrorDir);
        $this->mirrorDir = $mirrorDir;
    }

    /**
     * Save change to Git
     *
     * @param array $change
     * @return array
     */
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
                $changeToSave = json_encode($this->getData(),JSON_PRETTY_PRINT);
                file_put_contents($changeFile, $changeToSave);
                if ($operation == 'create') {
                    $this->git->add(dirname($changeFile));
                }
                break;
        }
        return $this->gitCommit($change);
    }

    /**
     * Commit changed files to git
     * @param type $change
     * @return type
     */
    public function gitCommit($change)
    {
        $id            = intval($change['id']);
        $inVersion     = intval($change['@in-version']);
        $evidence      = $change['@evidence'];
        $operation     = $change['@operation'];
        $changeMessage = $inVersion.' '.$evidence.' '.$operation.' #'.$id;
        if ($operation == 'change') {
            $changeMessage .= "\n".print_r($this->getLastDataChange(0), true);
        }
        $this->addStatusMessage('GIT: '.$changeMessage);
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

    public function getPreviousData($previous = 1)
    {
        $change     = ['id' => $this->getMyKey(), '@evidence' => $this->getEvidence()];
        $recordFile = str_replace($this->mirrorDir, '',
            $this->getChangeFile($change));
        return json_decode(implode("\n",
                $this->git->show($recordFile, $previous)), true);
    }

    public function getLastDataChange($previous)
    {
        $currentData  = $this->getData();
        $previousData = $this->getPreviousData($previous);
        return self::diff_recursive($currentData, $previousData);
    }
    /**
     * Recursively diff two arrays. This function expects the leaf levels to be
     * arrays of strings or null
     *
     * @param type $array1
     * @param type $array2
     * @return string
     */
    public static function diff_recursive($array1, $array2)
    {
        $difference = array();
        foreach ($array1 as $key => $value) {
            if (is_array($value) && isset($array2[$key])) { // it's an array and both have the key
                $new_diff         = self::diff_recursive($value, $array2[$key]);
                if (!empty($new_diff)) $difference[$key] = $new_diff;
            } else if (is_string($value) && !in_array($value, $array2)) { // the value is a string and it's not in array B
                $difference[$key] = $value;
            } else if (!is_numeric($key) && !array_key_exists($key, $array2)) { // the key is not numberic and is missing from array B
                $difference[$key] = '';
            }
        }
        return $difference;
    }
}
