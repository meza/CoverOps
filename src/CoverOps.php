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
 * The CoverOps class is responsible for ...
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
    private $coverage;

    public function __construct()
    {
        $path = explode(PATH_SEPARATOR, get_include_path());
        $path[] = realpath(dirname(__FILE__).'/../vendor/').'/php-code-coverage';
        $p = implode(PATH_SEPARATOR, $path);
        set_include_path($p);
        var_dump($path, $p);
        require_once 'PHP/CodeCoverage.php';
        require_once 'PHP/CodeCoverage/Report/Clover.php';
        require_once 'PHP/CodeCoverage/Report/HTML.php';

        $this->filter = new PHP_CodeCoverage_Filter();
        $this->coverage = new PHP_CodeCoverage(
            new PHP_CodeCoverage_Driver_Xdebug(),
            $this->filter
        );

    }

    public function initDir($dir)
    {
        $this->filter->addDirectoryToWhitelist($dir);
    }

    public function processDir($dir)
    {
        $f = opendir($dir);
        while($file = readdir($f)) {
            if ($file!='.' && $file!='..') {
                var_dump($dir.DIRECTORY_SEPARATOR.$file);
                $d = unserialize(file_get_contents($dir.DIRECTORY_SEPARATOR.$file));
                $this->coverage->append($d, $file);
            }
        }

    }
    
    public function writeHTML($toDir)
    {
        $writer   = new PHP_CodeCoverage_Report_HTML();
        $writer->process($this->coverage, $toDir);
    }

    public function writeCoverLog(HP$to)
    {
        $writer   = new PHP_CodeCoverage_Report_Clover();
        $writer->process($this->coverage, $to);
    }

}//end class


$cov = new CoverOps();
$cov->initDir('/home/meza/dev/ustream/trunk/apps/watershed.ustream.tv');
$cov->processDir('/home/meza/dev/ustream/tmp/logs/coverage');

$l = dirname(__FILE__).'/l';
if (false === file_exists($l)) {
    mkdir($l, 0777, true);
}
$cov->writeHTML($l);

?>