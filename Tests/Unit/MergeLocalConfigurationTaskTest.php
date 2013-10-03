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
 * Unit tests for MergeLocalConfigurationTask
 *
 */
class MergeLocalConfigurationTaskTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var MergeLocalConfigurationTask
     */
    protected $task = NULL;

    /**
     *
     * @var PhingFile
     */
    protected $base = NULL;

    /**
     *
     * @var PhingFile
     */
    protected $update = NULL;

    /**
     *
     * @var FileWriter
     */
    protected $fileWriter = NULL;

    public function setUp()
    {
        Phing::startup();

        $this->base = $this->getMockBuilder('PhingFile')
            ->setConstructorArgs(array(dirname(__FILE__) . '/../Fixtures/BaseLocalConfiguration.php'))
            ->setMethods(NULL)
            ->getMock();

        $this->update = $this->getMockBuilder('PhingFile')
            ->setConstructorArgs(array(dirname(__FILE__) . '/../Fixtures/UpdateLocalConfiguration.php'))
            ->setMethods(NULL)
            ->getMock();

        $_tempTargetFile = tempnam('/tmp', 'tmp');

        $tempTargetFile = $this->getMockBuilder('PhingFile')
            ->setConstructorArgs(array($_tempTargetFile))
            ->setMethods(NULL)
            ->getMock();

        $this->fileWriter = $this->getMockBuilder('FileWriter')
            ->setConstructorArgs(array($tempTargetFile))
            ->getMock();

        $this->task = new MergeLocalConfigurationTask();
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage You must specify the base file!
     */
    public function notSettingBaseFileThrowsAnException()
    {
        $this->task->main();
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage You must specify the update file!
     */
    public function notSettingUpdateFileThrowsAnException()
    {
        $this->task->setBase($this->base);

        $this->task->main();
    }

    /**
     *
     * @test
     */
    public function fileWriterIsUsedToWriteTheGeneratedPhpCode()
    {
        // extension configuration array added in remote configuration
        $testString1 = 'extConf';
        // InstallTool password is only set in update configuration (is overriden in base configuration as it is not set)
        $testString2 = "'installToolPassword' => '',";

        $this->fileWriter->expects($this->once())
            ->method('write')
            ->with(
                $this->logicalAnd(
                    $this->stringContains($testString1, FALSE),
                    $this->logicalNot(
                        $this->stringContains($testString2, FALSE)
                    )
                )
            );

        $this->task->setBase($this->base);
        $this->task->setUpdate($this->update);
        $this->task->addFileWriter($this->fileWriter);

        $this->task->main();
    }

    /**
     *
     * @test
     */
    public function mergeBehaviorChangesIfIncludeEmptyValuesIsFalse()
    {
        $testString1 = 'extConf';

        // install tool is empty in local configuration, leave remote setting intact...
        $testString2 = "'installToolPassword' => '2ccbdfbea4716a57da75f4c8e8d651ac',";

        $this->fileWriter->expects($this->once())
            ->method('write')
            ->with(
                $this->logicalAnd(
                    $this->stringContains($testString1),
                    $this->stringContains($testString2)
                )
            );

        $this->task->setBase($this->base);
        $this->task->setUpdate($this->update);
        $this->task->setIncludeEmptyValues(FALSE);
        $this->task->addFileWriter($this->fileWriter);

        $this->task->main();
    }
}
?>