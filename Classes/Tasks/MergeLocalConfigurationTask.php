<?php

include_once 'phing/Task.php';
include_once 'Utility/ArrayUtility.php';

class MergeLocalConfigurationTask extends Task {

	/**
	 *
	 * @var PhingFile
	 */
	protected $localFile = NULL;

	/**
	 *
	 * @var PhingFile
	 */
	protected $remoteFile = NULL;

	/**
	 *
	 * @var PhingFile
	 */
	protected $targetFile = NULL;

	/**
	 *
	 * @var FileWriter
	 */
	protected $fileWriter = NULL;

	/**
	 *
	 * @param PhingFile $localFile
	 * @return void
	 */
	public function setLocalFile(PhingFile $localFile) {
		$this->localFile = $localFile;
	}

	/**
	 *
	 * @return PhingFile
	 */
	public function getLocalFile() {
		return $this->file;
	}

	/**
	 *
	 * @param PhingFile $remoteFile
	 * @return void
	 */
	public function setRemoteFile(PhingFile $remoteFile) {
		$this->remoteFile = $remoteFile;
	}

	/**
	 *
	 * @return PhingFile
	 */
	public function getRemoteFile() {
		return $this->remoteFile;
	}

	/**
	 *
	 * @param PhingFile $targetFile
	 * @return void
	 */
	public function setTargetFile(PhingFile $targetFile) {
		$this->targetFile = $targetFile;
	}

	/**
	 *
	 * @return PhingFile
	 */
	public function getTargetFile() {
		return $this->targetFile;
	}

	public function addFileWriter(FileWriter $fileWriter = NULL) {
		if (NULL === $fileWriter) {
			$fileWriter = new FileWriter($this->localFile);
		}

		$this->fileWriter = $fileWriter;
	}

	public function main() {
		if (NULL === $this->localFile) {
			throw new BuildException('You must specify the locally generated file!');
		}

		if (NULL === $this->remoteFile) {
			throw new BuildException('You must specify the remote generated file!');
		}

		$localConfiguration = include($this->localFile->getAbsolutePath());
		$remoteConfiguration = include($this->remoteFile->getAbsolutePath());

		$mergedConfiguration = ArrayUtility::array_merge_recursive_overrule($remoteConfiguration, $localConfiguration, FALSE, FALSE);

		$configuration = new ConfigurationUtility($mergedConfiguration);
		$phpCode = $configuration->getLocalConfigurationArray();

		if (NULL === $this->fileWriter) {
			$this->addFileWriter();
		}

		$this->fileWriter->write($phpCode);
		$this->fileWriter->close();
	}
}
?>