<?php
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
    protected $localFile = NULL;

    /**
     *
     * @var PhingFile
     */
    protected $remoteFile = NULL;

    /**
     *
     * @var FileWriter
     */
    protected $fileWriter = NULL;

    public function setUp()
    {
        Phing::startup();

        $this->localFile = $this->getMockBuilder('PhingFile')
            ->setConstructorArgs(array(dirname(__FILE__) . '/../Fixtures/LocalLocalConfiguration.php'))
            ->setMethods(NULL)
            ->getMock();

        $this->remoteFile = $this->getMockBuilder('PhingFile')
            ->setConstructorArgs(array(dirname(__FILE__) . '/../Fixtures/RemoteLocalConfiguration.php'))
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
     * @expectedExceptionMessage You must specify the locally generated file!
     */
    public function notSettingLocalFileThrowsAnException()
    {
        $this->task->main();
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage You must specify the remote generated file!
     */
    public function notSettingRemoteFileThrowsAnException()
    {
        $this->task->setLocalFile($this->localFile);

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
        // InstallTool password is only set in remote configuration (is overriden as it is not set in local configuration)
        $testString2 = '2ccbdfbea4716a57da75f4c8e8d651ac';

        $this->fileWriter->expects($this->once())
            ->method('write')
            ->with(
                $this->logicalAnd(
                    $this->stringContains($testString1),
                    $this->logicalNot(
                        $this->stringContains($testString2)
                    )
                )
            );

        $this->task->setLocalFile($this->localFile);
        $this->task->setRemoteFile($this->remoteFile);
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

        $this->task->setLocalFile($this->localFile);
        $this->task->setRemoteFile($this->remoteFile);
        $this->task->setIncludeEmptyValues(FALSE);
        $this->task->addFileWriter($this->fileWriter);

        $this->task->main();
    }
}
?>