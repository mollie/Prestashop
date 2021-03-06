<?php

/**
 * Unit test class for the CodeAnalyzer sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace MolliePrefix\PHP_CodeSniffer\Standards\Zend\Tests\Debug;

use MolliePrefix\PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use MolliePrefix\PHP_CodeSniffer\Config;
class CodeAnalyzerUnitTest extends \MolliePrefix\PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest
{
    /**
     * Should this test be skipped for some reason.
     *
     * @return void
     */
    protected function shouldSkipTest()
    {
        $analyzerPath = \MolliePrefix\PHP_CodeSniffer\Config::getExecutablePath('zend_ca');
        if ($analyzerPath === null) {
            return \true;
        }
        return \false;
    }
    //end shouldSkipTest()
    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList()
    {
        return [];
    }
    //end getErrorList()
    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return [2 => 1];
    }
    //end getWarningList()
}
//end class
