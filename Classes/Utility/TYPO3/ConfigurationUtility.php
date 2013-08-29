<?php

/***************************************************************
 * Copyright notice
 *
 * (c) 2012 Helge Funk <helge.funk@e-net.info>
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
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

include_once 'Utility/TYPO3/ArrayUtility.php';

/**
 * Configuration Utilities
 *
 */
class ConfigurationUtility {

    /**
     *
     * @see typo3/sysext/core/Classes/Configuration/ConfigurationManager.php::writeLocalConfiguration()
     *
     * @var string
     */
    const OUTPUT_TEMPLATE = '<?php%newline%return %localConfigurationArray%;%newline%?>';

    protected $configuration = array();

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getLocalConfigurationArray()
    {
        $phpCode = strtr(self::OUTPUT_TEMPLATE, array(
            '%newline%' => chr(10),
            '%localConfigurationArray%' => $this->getExportedLocalConfigurationArray(),
        ));

        return $phpCode;
    }

    protected function getExportedLocalConfigurationArray()
    {
        $sortedConfiguration = ArrayUtility::sortByKeyRecursive($this->configuration);
        $renumberedArray = ArrayUtility::renumberKeysToAvoidLeapsIfKeysAreAllNumeric($sortedConfiguration);

        return ArrayUtility::arrayExport($renumberedArray);
    }
}
?>