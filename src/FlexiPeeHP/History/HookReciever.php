<?php
/**
 * FlexiBee History - WebHook handler
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */

namespace FlexiPeeHP\History;

/**
 * Description of HookReciver
 *
 * @author vitex
 */
class HookReciever extends \Ease\Brick
{
    public $format        = 'json';
    public $changes       = null;
    public $globalVersion = null;
    public $lastFileName  = '';

    /**
     * Posledni zpracovana verze
     * @var int
     */
    public $lastProcessedVersion = null;

    /**
     * Where to save FlexiBee changes
     * @var string path to dir
     */
    public $mirrorDir;

    /**
     * History Keeper
     * @var History
     */
    public $libraryan = null;

    /**
     * Prijmac WebHooku
     */
    public function __construct()
    {
        parent::__construct();
        $this->lastProcessedVersion = $this->getLastProcessedVersion();
        $this->libraryan            = new History();
    }

    /**
     * Poslouchá standartní vstup
     *
     * @return string zaslaná data
     */
    public static function listen()
    {
        $input     = null;
        $inputJSON = file_get_contents('php://input');
        if (strlen($inputJSON)) {
            $input = json_decode($inputJSON, TRUE); //convert JSON into array
        }
        return $input;
    }

    /**
     * Zpracuje změny
     */
    function processChanges()
    {
        if (count($this->changes)) {
            foreach ($this->changes as $change) {
                $this->libraryan->saveChange($change);
            }
        } else {
            $this->addStatusMessage('No Data To Process', 'warning');
        }
    }

    /**
     * Převezme změny
     * 
     * @link https://www.flexibee.eu/api/dokumentace/ref/changes-api/ Changes API
     * @param array $changes pole změn
     * @return int Globální verze poslední změny
     */
    public function takeChanges($changes)
    {
        $result = null;
        if (!is_array($changes)) {
            \Ease\Shared::logger()->addToLog(_('Empty WebHook request'),
                'Warning');
        } else {
            if (array_key_exists('winstrom', $changes)) {
                $this->globalVersion = intval($changes['winstrom']['@globalVersion']);
                $this->changes       = $changes['winstrom']['changes'];
            }
            $result = $this->globalVersion;
        }
        return $result;
    }

    /**
     * Ulozi posledni zpracovanou verzi
     *
     * @param int $version
     */
    public function saveLastProcessedVersion($version)
    {
        $this->lastProcessedVersion = $version;
        file_put_contents(sys_get_temp_dir().'/lastFlexiBeeVersion',
            $this->lastProcessedVersion);
    }

    /**
     * Nacte posledni zpracovanou verzi
     *
     * @return int $version
     */
    public function getLastProcessedVersion()
    {
        $lastProcessedVersion = null;
        $versionFile          = sys_get_temp_dir().'/lastFlexiBeeVersion';
        if (file_exists($versionFile)) {
            $lastProcessedVersion = intval(file_get_contents($versionFile));
        }
        return $lastProcessedVersion;
    }

    /**
     * @return string Filename for current webhook data save
     */
    public static function getSaveName()
    {
        $host = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR'];
        return ('flexibee-changes-'.$host.'_'.time().'.json');
    }

    /**
     * Where to store FlexiBee history files
     * @param string $mirrorDir path
     */
    public function setMirrorDir($mirrorDir)
    {
        $this->libraryan->setMirrorDir($mirrorDir);
    }
}
