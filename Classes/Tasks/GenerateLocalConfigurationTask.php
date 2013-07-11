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
include_once 'Utility/ArrayUtility.php';
include_once 'Utility/ConfigurationUtility.php';

/**
 * Writes a the TYPO3 CMS LocalConfiguration.php file
 *
 * Usage in a phing build file
 *
 * 1. Import via TaskdefTask:
 *
 * <taskdef classname="Tasks.GenerateLocalConfigurationTask" name="generatelocalconfiguration" />
 *
 * 2. Call task:
 *
 * <generatelocalconfiguration file="path/to/typo3conf/LocalConfiguration.php" propertyprefix="LocalConfiguration" />
 * 
 * @author Thomas Juhnke <tommy@van-tomas.de>
 * @package Tasks
 */
class GenerateLocalConfigurationTask extends Task {

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
	 *
	 * @return void
	 */
	public function main() {
		if (NULL === $this->file) {
			throw new BuildException('You must specify the file attribute!');
		}

		if (NULL === $this->fileWriter) {
			$this->addFileWriter();
		}

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
		$configuration = new ConfigurationUtility($this->localConfiguration);
		$phpCode = $configuration->getLocalConfigurationArray();

		$this->fileWriter->write($phpCode);
		$this->fileWriter->close();
	}
}
?>