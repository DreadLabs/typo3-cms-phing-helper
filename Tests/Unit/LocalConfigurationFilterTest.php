<?php
class LocalConfigurationFilterTestCase extends PHPUnit_Framework_TestCase {

	protected $reader = NULL;

	protected $file = NULL;

	protected $filter = NULL;

	public function setUp() {
		Phing::startup();

		$this->reader = $this->getMockBuilder('StringReader')
			->setConstructorArgs(array(''))
			->getMock();

		$this->file = $this->getMockBuilder('PhingFile')
			->setConstructorArgs(array(realpath(dirname(__FILE__) . '/../Fixtures/DefaultConfiguration.php')))
			->getMock();

		$this->file
			->expects($this->any())
			->method('getAbsolutePath')
			->will(
				$this->returnValue(realpath(dirname(__FILE__) . '/../Fixtures/DefaultConfiguration.php'))
			);

		$this->filter = new LocalConfigurationFilter($this->reader);
		$this->filter->setFile($this->file);
	}

	/**
	 *
	 * @test
	 */
	public function defaultConfigurationArrayIsTransformedToPhingPropertyFormat() {
		$output = $this->filter->read();

		$this->assertContains('GFX.image_processing=', $output);
	}

	/**
	 *
	 * @test
	 */
	public function phingPropertyContainsTheDefaultConfigurationValue() {
		$output = $this->filter->read();

		$this->assertContains('GFX.imagefile_ext=gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai', $output);
	}
}
?>