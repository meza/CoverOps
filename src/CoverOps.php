<?php
/**
 * CoverOps.php
 *
 * Holds the CoverOps class
 *
 * PHP Version: PHP 5
 *
 * @category File
 * @package  CoverOps
 * @author   meza <meza@meza.hu>
 * @license  GPL3.0
 *                    GNU GENERAL PUBLIC LICENSE
 *                       Version 3, 29 June 2007
 *
 * Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
 * Everyone is permitted to copy and distribute verbatim copies
 * of this license document, but changing it is not allowed.
 * @link     http://www.meza.hu
 */

/**
 * The CoverOps class is responsible for parsing coverage log files to a
 * form that can be overviwed by human or machine.
 *
 * PHP Version: PHP 5
 *
 * @category Class
 * @package  CoverOps
 * @author   meza <meza@meza.hu>
 * @license  GPL3.0
 *                    GNU GENERAL PUBLIC LICENSE
 *                       Version 3, 29 June 2007
 *
 * Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
 * Everyone is permitted to copy and distribute verbatim copies
 * of this license document, but changing it is not allowed.
 * @link     http://www.meza.hu
 */
class CoverOps
{

    /**
     * @var PHP_CodeCoverage;
     */
    private $_coverage;


    /**
     * Constructs the object
     *
     * @return CoverOps
     */
    public function __construct()
    {
        $path = explode(PATH_SEPARATOR, get_include_path());
        array_unshift($path, realpath(dirname(__FILE__).'/../vendor/').'/php-code-coverage');
        $p = implode(PATH_SEPARATOR, $path);
        set_include_path($p);
        require_once 'PHP/CodeCoverage.php';
        require_once 'PHP/CodeCoverage/Report/Clover.php';
        require_once 'PHP/CodeCoverage/Report/HTML.php';

        $this->filter = new PHP_CodeCoverage_Filter();
        $this->_coverage = new PHP_CodeCoverage(
            new PHP_CodeCoverage_Driver_Xdebug(),
            $this->filter
        );

    }//end __construct()


    /**
     * Add a directory to the white list of the coverage.
     *
     * @param string $directory The directory to add
     *
     * @return void
     */
    public function addWhitelistDir($directory)
    {
        $this->filter->addDirectoryToWhitelist($directory);

    }//end addWhitelistDir()


    /**
     * Add a file to the white list of the coverage.
     *
     * @param string $dir The directory to add
     *
     * @return void
     */
    public function addWhitelistFile($filename)
    {
        $this->filter->addFileToWhitelist($filename);

    }//end addWhitelistFile()


    /**
     * Add a directory to the black list of the coverage.
     *
     * @param string $directory The directory to add
     *
     * @return void
     */
    public function addBlacklistDir($directory)
    {
        $this->filter->addDirectoryToBlacklist($directory);

    }//end addBlacklistDir()


    /**
     * Add a file to the black list of the coverage.
     *
     * @param string $dir The directory to add
     *
     * @return void
     */
    public function addBlacklistFile($filename)
    {
        $this->filter->addFileToBlacklist($filename);

    }//end addBlacklistFile()


    /**
     * Process a directory of log files
     *
     * @param string $dir The dir name to process
     *
     * @return void
     */
    public function processLogDir($dir)
    {
        $f = opendir($dir);
        while($file = readdir($f)) {
            if ($file!='.' && $file!='..') {
                $this->processLogFile($dir.DIRECTORY_SEPARATOR.$file);
            }
        }

    }//end processLogDir()


    /**
     * Process a log file (containing serialized coverage data)
     *
     * @param string $filename The filename to process
     *
     * @return void
     */
    public function processLogFile($filename)
    {
        $this->addSerializedCoverageData(file_get_contents($filename));
        
    }//end processLogFile()


    /**
     * Add a serialized coverage data unit to the overall coverage data.
     *
     * @param string $data Serialized array of the coverage data.
     *
     * @return void
     */
    public function addSerializedCoverageData($data)
    {
        $this->addCoverageData(unserialize($data));

    }//end addSerializedCoverageData()


    /**
     * Add coverage data to the overall log.
     *
     * @param array $data The coverage data to append.
     *
     * @return void
     */
    public function addCoverageData(array $data)
    {
        $this->_coverage->append($data, $filename);

    }//end addCoverageData()


    /**
     * Create the HTML report to the specified directory.
     *
     * @param string $toDir The directory for the html output.
     *
     * @return void
     */
    public function writeHTML($toDir)
    {
        $writer   = new PHP_CodeCoverage_Report_HTML();
        $writer->process($this->_coverage, $toDir);

    }//end writeHTML()


    /**
     * Create a clover.xml report file to the specified location.
     *
     * @param string $to The filename to put the log to.
     *
     * @return void
     */
    public function writeClover($to)
    {
        $writer   = new PHP_CodeCoverage_Report_Clover();
        $writer->process($this->_coverage, $to);

    }//end writeClover()


}//end class

?>