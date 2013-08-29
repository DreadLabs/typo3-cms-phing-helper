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
 * Unit tests for GenerateLocalConfigurationTask
 *
 */
class GenerateLocalConfigurationTaskTest extends PHPUnit_Framework_TestCase
{

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

    public function setUp()
    {
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
    public function aPropertyTaskShouldBeUsedToLoadGeneratedLocalConfigurationProperties()
    {
        $properties = $this->project->getProperties();

        $this->assertNotEmpty($properties);
    }

    /**
     *
     * @test
     */
    public function aLocalConfigurationPropertyShouldBeAvailableInTheProjectProperties()
    {
        $db_name = $this->project->getProperty('LocalConfiguration.DB.database');

        $this->assertEquals($db_name, 'mydb');
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage You must specify the file attribute!
     */
    public function notSettingTheFileAttributeThrowsAnException()
    {
        $this->task->main();
    }

    /**
     *
     * @test
     */
    public function fileWriterIsUsedToWriteTheGeneratedPhpCode()
    {
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

        $this->task->main();
    }
}
?>