<?php
class GenerateLocalConfigurationTaskTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 * @var Project
	 */
	protected $project = NULL;

	/**
	 *
	 * @var GenerateLocalConfigurationTask
	 */
	protected $task = NULL;

	protected $file = NULL;

	public function setUp() {
		Phing::startup();

		$this->project = $this->getMockBuilder('Project')
			->setMethods(NULL)
			->getMock(NULL);

		$tempTargetFile = tempnam('/tmp', 'tmp');

		$this->file = $this->getMockBuilder('PhingFile')
			->setConstructorArgs(array($tempTargetFile))
			->setMethods(NULL)
			->getMock();

		$propertyTask = $this->getMockBuilder('PropertyTask')
			->setMethods(NULL)
			->getMock();

		$propertyTask->setProject($this->project);
		$propertyTask->setFile(dirname(__FILE__) . '/../Fixtures/LocalConfiguration.properties');
		$propertyTask->setPrefix('LocalConfiguration.');
		$propertyTask->main();

		$this->task = new GenerateLocalConfigurationTask();
		$this->task->setProject($this->project);
	}

	/**
	 *
	 * @test
	 */
	public function aPropertyTaskShouldBeUsedToLoadGeneratedLocalConfigurationProperties() {
		$properties = $this->project->getProperties();

		$this->assertNotEmpty($properties);
	}

	/**
	 *
	 * @test
	 */
	public function aLocalConfigurationPropertyShouldBeAvailableInTheProjectProperties() {
		$db_name = $this->project->getProperty('LocalConfiguration.DB.database');

		$this->assertEquals($db_name, 'mydb');
	}

	/**
	 *
	 * @test
	 * @expectedException BuildException
	 * @expectedExceptionMessage You must specify the file attribute!
	 */
	public function notSettingTheFileAttributeThrowsAnException() {
		$this->task->init();
		$this->task->main();
	}

	/**
	 *
	 * @test
	 */
	public function fileWriterIsUsedToWriteTheGeneratedPhpCode() {
		$fileWriter = $this->getMockBuilder('FileWriter')
			->setConstructorArgs(array($this->file))
			->getMock();

		// SYS.textfile_ext value
		$testString = 'txt,html,htm,css,tmpl,js,sql,xml,csv,php,php3,php4,php5,php6,phpsh,inc,phtml';

		$fileWriter->expects($this->once())
			->method('write')
			->with($this->stringContains($testString));

		$this->task->setFile($this->file);
		$this->task->addFileWriter($fileWriter);

		$this->task->init();
		$this->task->main();
	}
}
?>