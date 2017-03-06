<?php
/**
 * FlexiBee History - Git Storage
 *
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2017 Vitex Software (G)
 */

namespace FlexiPeeHP\History;

/**
 * Description of GitStorage
 *
 * @author vitex
 */
class GitStorage extends \SebastianBergmann\Git\Git
{

    /**
     * Initialize Git repository
     *
     * @return array answer
     */
    public function init()
    {
        return $this->execute('init');
    }

    /**
     * Add item(s) to git repo
     *
     * @param array $path
     * @return array
     */
    public function add($path)
    {
        return $this->execute('add '.$path);
    }

    /**
     * Commit changes to git
     *
     * @param string $message
     * @return array
     */
    public function commit($message)
    {
        return $this->execute('commit -a -m "'.$message.'"');
    }

    /**
     * Remove file From GIT
     *
     * @param string $file
     * @return array
     */
    public function rm($file)
    {
        return $this->execute('rm '.$file);
    }

    /**
     * Set Git's config value
     * 
     * @param string $key
     * @param srting $value
     * @return array
     */
    public function config($key, $value)
    {
        return $this->execute('config '.$key.' '.$value);
    }

    /**
     * Show previous version of file
     *
     * @param string $file path
     * @return array
     */
    public function show($file, $prevchange = 0)
    {
        return $this->execute('show  HEAD~'.$prevchange.':'.$file);
    }

    /**
     * Get HASH of prvious commit of given file
     *
     * @param string $file path
     * @return string SHA1 commit hash
     */
    public function getPreviousCommitHash($file)
    {
        return current($this->execute('log -n 1 --format=format:%H '.$file));
    }
}
