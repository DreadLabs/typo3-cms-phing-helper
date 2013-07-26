<?php

/**
 * NOTE: all methods within this utility class come from the TYPO3 Project. I'm
 * no license specialist so I included both - the Phing and TYPO3 Project Copyright
 * notices. Hopefully I'm not offending someones rights with this and I appreciate
 * any help solving this problem.
 */

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

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

include_once 'Utility/ArrayUtility.php';

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