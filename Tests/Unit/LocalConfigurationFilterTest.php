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

/**
 * Unit tests for LocalConfigurationFilter
 *
 */
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