<?php

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

include_once 'phing/Task.php';

/**
 * Writes a the TYPO3 CMS LocalConfiguration.php file
 *
 * Usage in a phing build file
 *
 * <generatelocalconfiguration file="path/to/typo3conf/LocalConfiguration.php" propertyprefix="LocalConfiguration" />
 * 
 * @author Thomas Juhnke <tommy@van-tomas.de>
 * @package Tasks
 */
class GenerateLocalConfigurationTask extends Task {

	/**
	 *
	 * @see typo3/sysext/core/Classes/Configuration/ConfigurationManager.php::writeLocalConfiguration()
	 *
	 * @var string
	 */
	const OUTPUT_TEMPLATE = '<?php%newline%return %localConfigurationArray%;%newline%?>';

	/**
	 * The target file to write the LocalConfiguration into
	 *
	 * @var PhingFile
	 */
	protected $file = NULL;

	/**
	 * Prefix for LocalConfiguration properties loaded with PropertyTask
	 *
	 * @var string
	 */
	protected $propertyPrefix = 'LocalConfiguration';

	/**
	 *
	 * @var array
	 */
	private $localConfiguration = array();

	/**
	 *
	 * @var FileWriter
	 */
	private $fileWriter = NULL;

	/**
	 * sets the target file to write LocalConfiguration into
	 *
	 * @param PhingFile $file
	 * @return void
	 */
	public function setFile(PhingFile $file) {
		$this->file = $file;
	}

	/**
	 *
	 * @return PhingFile
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 *
	 * @param string $file
	 * @return void
	 */
	public function setPropertyPrefix($propertyPrefix) {
		$this->propertyPrefix = $propertyPrefix;
	}

	/**
	 *
	 * @return string
	 */
	public function getPropertyPrefix() {
		return $this->propertyPrefix;
	}

	public function addFileWriter(FileWriter $fileWriter = NULL) {
		if (NULL === $fileWriter) {
			$fileWriter = new FileWriter($this->file);
		}

		$this->fileWriter = $fileWriter;
	}

	/**
	 * initializes the task
	 *
	 * @return void
	 * @throws BuildException
	 */
	public function init() {
		if (NULL === $this->file) {
			throw new BuildException('You must specify the file attribute!');
		}

		if (NULL === $this->fileWriter) {
			$this->addFileWriter();
		}
	}

	/**
	 *
	 * @return void
	 */
	public function main() {
		$this->transformPropertiesToArray();

		$this->writeLocalConfigurationArray();
	}

	/**
	 * transforms the flat property list into a local configuration array
	 *
	 * Example
	 *
	 * Given
	 * LocalConfiguration.DB.database=my_database_name
	 *
	 * Result
	 * $this->localConfiguration = array(
	 * 	'DB' => array(
	 * 		'database' => 'my_database_name'
	 * 	)
	 * );
	 *
	 * @return void
	 */
	protected function transformPropertiesToArray() {
		$properties = $this->project->getProperties();

		foreach ($properties as $propertyName => $propertyValue) {
			if (0 !== strpos($propertyName, $this->propertyPrefix . '.')) {
				continue;
			}

			list(, $mainKey, $subKey) = $this->splitPropertyName($propertyName);

			$this->addLocalConfigurationValue($mainKey, $subKey, $propertyValue);
		}
	}

	protected function splitPropertyName($propertyName) {
		$propertyParts = explode('.', $propertyName, 3);

		return $propertyParts;
	}

	protected function addLocalConfigurationValue($mainKey, $subKey, $propertyValue) {
		if (FALSE === isset($this->localConfiguration[$mainKey])) {
			$this->localConfiguration[$mainKey] = array();
		}

		$this->localConfiguration[$mainKey][$subKey] = $propertyValue;
	}

	protected function writeLocalConfigurationArray() {
		$phpCode = strtr(self::OUTPUT_TEMPLATE, array(
			'%newline%' => chr(10),
			'%localConfigurationArray%' => $this->getExportedLocalConfigurationArray(),
		));

		$this->fileWriter->write($phpCode);
		$this->fileWriter->close();
	}

	protected function getExportedLocalConfigurationArray() {
		$renumberedArray = $this->renumberKeysToAvoidLeapsIfKeysAreAllNumeric($this->localConfiguration);

		return $this->arrayExport($renumberedArray);
	}

	/**
	 * Renumber the keys of an array to avoid leaps is keys are all numeric.
	 *
	 * Is called recursively for nested arrays.
	 *
	 * Example:
	 *
	 * Given
	 *  array(0 => 'Zero' 1 => 'One', 2 => 'Two', 4 => 'Three')
	 * as input, it will return
	 *  array(0 => 'Zero' 1 => 'One', 2 => 'Two', 3 => 'Three')
	 *
	 * Will treat keys string representations of number (ie. '1') equal to the
	 * numeric value (ie. 1).
	 *
	 * Example:
	 * Given
	 *  array('0' => 'Zero', '1' => 'One' )
	 * it will return
	 *  array(0 => 'Zero', 1 => 'One')
	 *
	 * @author Susanne Moog <typo3@susanne-moog.de>
	 * @see typo3/sysext/core/Classes/Utility/ArrayUtility.php
	 *
	 * @param array $array Input array
	 * @param integer $level Internal level used for recursion, do *not* set from outside!
	 * @return array
	 */
	private function renumberKeysToAvoidLeapsIfKeysAreAllNumeric(array $array = array(), $level = 0) {
		$level++;
		$allKeysAreNumeric = TRUE;
		foreach (array_keys($array) as $key) {
			if (is_numeric($key) === FALSE) {
				$allKeysAreNumeric = FALSE;
				break;
			}
		}
		$renumberedArray = $array;
		if ($allKeysAreNumeric === TRUE) {
			$renumberedArray = array_values($array);
		}
		foreach ($renumberedArray as $key => $value) {
			if (is_array($value)) {
				$renumberedArray[$key] = self::renumberKeysToAvoidLeapsIfKeysAreAllNumeric($value, $level);
			}
		}

		return $renumberedArray;
	}

	/**
	 * Exports an array as string.
	 * Similar to var_export(), but representation follows the TYPO3 core CGL.
	 *
	 * See unit tests for detailed examples
	 *
	 * @author Susanne Moog <typo3@susanne-moog.de>
	 * @see typo3/sysext/core/Classes/Utility/ArrayUtility.php
	 *
	 * @param array $array Array to export
	 * @param integer $level Internal level used for recursion, do *not* set from outside!
	 * @return string String representation of array
	 * @throws \RuntimeException
	 */
	private function arrayExport(array $array = array(), $level = 0) {
		$lines = 'array(' . chr(10);
		$level++;
		$writeKeyIndex = FALSE;
		$expectedKeyIndex = 0;
		foreach ($array as $key => $value) {
			if ($key === $expectedKeyIndex) {
				$expectedKeyIndex++;
			} else {
				// Found a non integer or non consecutive key, so we can break here
				$writeKeyIndex = TRUE;
				break;
			}
		}
		foreach ($array as $key => $value) {
			// Indention
			$lines .= str_repeat(chr(9), $level);
			if ($writeKeyIndex) {
				// Numeric / string keys
				$lines .= is_int($key) ? $key . ' => ' : '\'' . $key . '\' => ';
			}
			if (is_array($value)) {
				if (count($value) > 0) {
					$lines .= self::arrayExport($value, $level);
				} else {
					$lines .= 'array(),' . chr(10);
				}
			} elseif (is_int($value) || is_float($value)) {
				$lines .= $value . ',' . chr(10);
			} elseif (is_null($value)) {
				$lines .= 'NULL' . ',' . chr(10);
			} elseif (is_bool($value)) {
				$lines .= $value ? 'TRUE' : 'FALSE';
				$lines .= ',' . chr(10);
			} elseif (is_string($value)) {
				// Quote \ to \\
				$stringContent = str_replace('\\', '\\\\', $value);
				// Quote ' to \'
				$stringContent = str_replace('\'', '\\\'', $stringContent);
				$lines .= '\'' . $stringContent . '\'' . ',' . chr(10);
			} else {
				throw new RuntimeException('Objects are not supported', 1342294986);
			}
		}
		$lines .= str_repeat(chr(9), ($level - 1)) . ')' . ($level - 1 == 0 ? '' : ',' . chr(10));

		return $lines;
	}
}
?>