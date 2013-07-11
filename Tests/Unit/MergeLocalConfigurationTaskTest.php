<?php
class MergeLocalConfigurationTaskTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 * @var MergeLocalConfigurationTask
	 */
	protected $task = NULL;

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

	public function setUp() {
		Phing::startup();

		$this->localFile = $this->getMockBuilder('PhingFile')
			->setConstructorArgs(array(dirname(__FILE__) . '/../Fixtures/LocalLocalConfiguration.php'))
			->setMethods(NULL)
			->getMock();

		$this->remoteFile = $this->getMockBuilder('PhingFile')
			->setConstructorArgs(array(dirname(__FILE__) . '/../Fixtures/RemoteLocalConfiguration.php'))
			->setMethods(NULL)
			->getMock();

		$this->task = new MergeLocalConfigurationTask();
	}

	/**
	 *
	 * @test
	 * @expectedException BuildException
	 * @expectedExceptionMessage You must specify the locally generated file!
	 */
	public function notSettingLocalFileThrowsAnException() {
		$this->task->main();
	}

	/**
	 *
	 * @test
	 * @expectedException BuildException
	 * @expectedExceptionMessage You must specify the remote generated file!
	 */
	public function notSettingRemoteFileThrowsAnException() {
		$this->task->setLocalFile($this->localFile);

		$this->task->main();
	}

	/**
	 *
	 * @test
	 */
	public function fileWriterIsUsedToWriteTheGeneratedPhpCode() {
		$_tempTargetFile = tempnam('/tmp', 'tmp');

		$tempTargetFile = $this->getMockBuilder('PhingFile')
			->setConstructorArgs(array($_tempTargetFile))
			->setMethods(NULL)
			->getMock();

		$fileWriter = $this->getMockBuilder('FileWriter')
			->setConstructorArgs(array($tempTargetFile))
			->getMock();

		// extension configuration array added in remote configuration
		$testString1 = 'extConf';
		// InstallTool password is only set in remote configuration
		$testString2 = '2ccbdfbea4716a57da75f4c8e8d651ac';

		$fileWriter->expects($this->once())
			->method('write')
			->with(
				$this->logicalAnd(
					$this->stringContains($testString1),
					$this->stringContains($testString2)
				)
			);

		$this->task->setLocalFile($this->localFile);
		$this->task->setRemoteFile($this->remoteFile);
		$this->task->addFileWriter($fileWriter);

		$this->task->main();
	}
}
?>