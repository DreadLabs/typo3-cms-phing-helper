<?php
class LocalConfigurationFilterTestCase extends PHPUnit_Framework_TestCase {

	protected $reader = NULL;

	protected $file = NULL;

	protected $filter = NULL;

	public function setUp() {
		Phing::startup();

		$this->reader = $this->getMockBuilder('FileReader')
			->setConstructorArgs(array(realpath(dirname(__FILE__) . '/../Fixtures/DefaultConfiguration.php')))
			->setMethods(NULL)
			->getMock();

		$this->filter = new LocalConfigurationFilter($this->reader);
	}

	/**
	 *
	 * @test
	 */
	public function defaultConfigurationArrayIsTransformedToPhingPropertyFormat() {
		$this->filter->setTYPO3Version('6.0.6');
		$output = $this->filter->read();

		$this->assertContains('GFX.image_processing=', $output);
	}

	/**
	 *
	 * @test
	 */
	public function phingPropertyContainsTheDefaultConfigurationValue() {
		$this->filter->setTYPO3Version('6.0.6');
		$output = $this->filter->read();

		$this->assertContains('GFX.imagefile_ext=gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai', $output);
	}

	/**
	 *
	 * @test
	 * @expectedException BuildException
	 * @expectedExceptionMessage You must pass a TYPO3 version via the corresponding parameter in your build file!
	 */
	public function paramTYPO3VersionIsRequired() {
		$output = $this->filter->read();
	}

	/**
	 *
	 * @test
	 */
	public function typo3VersionParameterIsAvailableInTheGeneratedPropertyFile() {
		$this->filter->setTYPO3Version('6.0.6');
		$output = $this->filter->read();

		$this->assertContains('HTTP.userAgent=TYPO3/6.0.6', $output);
	}
}
?>