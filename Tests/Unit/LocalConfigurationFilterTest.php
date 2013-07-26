<?php
class LocalConfigurationFilterTestCase extends PHPUnit_Framework_TestCase
{

    protected $reader = NULL;

    protected $file = NULL;

    protected $filter = NULL;

    public function setUp()
    {
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
    public function defaultConfigurationArrayIsTransformedToPhingPropertyFormat()
    {
        $this->filter->setInitialized(TRUE);
        $this->filter->setTYPO3Version('6.0.6');
        $output = $this->filter->read();

        $this->assertContains('GFX.image_processing=', $output);
    }

    /**
     *
     * @test
     */
    public function phingPropertyContainsTheDefaultConfigurationValue()
    {
        $this->filter->setInitialized(TRUE);
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
    public function paramTYPO3VersionIsRequired()
    {
        $output = $this->filter->read();
    }

    /**
     *
     * @test
     */
    public function typo3VersionParameterIsAvailableInTheGeneratedPropertyFile()
    {
        $this->filter->setTYPO3Version('6.0.6');
        $this->filter->setInitialized(TRUE);
        $output = $this->filter->read();

        $this->assertContains('HTTP.userAgent=TYPO3/6.0.6', $output);
    }

    /**
     *
     * @test
     */
    public function filterInitializesItself()
    {
        $parameters = array();

        $param = new Parameter();
        $param->setName('TYPO3Version');
        $param->setValue('99.99.99');

        array_push($parameters, $param);

        $this->filter->setParameters($parameters);

        $output = $this->filter->read();

        $this->assertContains('HTTP.userAgent=TYPO3/99.99.99', $output);
    }

    /**
     *
     * @test
     */
    public function filterInitializationAlsoRespectsCaseInsensitivity()
    {
        $parameters = array();

        $param = new Parameter();
        $param->setName('typo3version');
        $param->setValue('1.2.3');

        array_push($parameters, $param);

        $this->filter->setParameters($parameters);

        $output = $this->filter->read();

        $this->assertContains('HTTP.userAgent=TYPO3/1.2.3', $output);
    }
}
?>