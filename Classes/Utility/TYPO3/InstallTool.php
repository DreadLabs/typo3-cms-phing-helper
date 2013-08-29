<?php

/***************************************************************
 * Copyright notice
 *
 * (c) 1999-2013 Kasper Skårhøj (kasperYYYY@typo3.com)
 * (c) 2013 Christian Kuhn <lolli@schwarzbu.ch>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a wrapper + bugfixing container for some methods from the InstallTool
 *
 * path in TYPO3 4.7.x: ext:sysext/install/mod/class.tx_install.php
 * path in TYPO3 6.0.x + 6.1.x: \TYPO3\CMS\Install\Installer
 * path in TYPO3 6.2.x+: \TYPO3\CMS\Install\Controller\Action\Tool
 */
class InstallTool
{

    /**
     *
     * @var string
     */
    protected $arrayKeyPattern = '/["\']([[:alnum:]_-]*)["\'][[:space:]]*=>(.*)/i';

    /**
     *
     * @var string
     */
    protected $commentPattern = '/,[\\t\\s]*\\/\\/(.*)/i';

    /**
     * Make an array of the comments in the t3lib/stddb/DefaultConfiguration.php file
     *
     * @note: the regular expression patterns are outsourced into class members
     *
     * @param string $string The contents of the t3lib/stddb/DefaultConfiguration.php file
     * @param array $mainArray
     * @param array $commentArray
     * @return array
     */
    public function getDefaultConfigArrayComments($string)
    {
        $commentArray = array();
        $lines = explode(chr(10), $string);

        $in = 0;
        $mainKey = '';

        foreach ($lines as $lc) {
            $lc = trim($lc);
            if ($in) {
                if (!strcmp($lc, ');')) {
                    $in = 0;
                } else {
                    if (preg_match($this->arrayKeyPattern, $lc, $reg)) {
                        preg_match($this->commentPattern, $reg[2], $creg);

                        // @note: $theComment assignment fails under strict environment (UndefinedIndex); isset() was added
                        $theComment = isset($creg[1]) ? $creg[1] : '';
                        $theComment = trim($theComment);

                        if (substr(strtolower(trim($reg[2])), 0, 5) == 'array'
                                && !strcmp($reg[1], strtoupper($reg[1]))) {
                            $mainKey = trim($reg[1]);
                        } elseif ($mainKey) {
                            $commentArray[$mainKey][$reg[1]] = $theComment;
                        }
                    }
                }
            }
            if (!strcmp($lc, 'return array(')) {
                $in = 1;
            }
        }

        return $commentArray;
    }
}
?>