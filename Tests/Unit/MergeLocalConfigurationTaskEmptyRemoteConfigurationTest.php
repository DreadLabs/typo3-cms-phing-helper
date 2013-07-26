<?php
class MergeLocalConfigurationTaskEmptyRemoteConfigurationTest extends PHPUnit_Framework_TestCase
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
            ->setConstructorArgs(array(dirname(__FILE__) . '/../Fixtures/RemoteEmptyLocalConfiguration.php'))
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
     */
    public function mergingEmptyOrUnavailableRemoteLocalConfigurationShouldLeaveLocalConfigurationIntact()
    {
        $this->fileWriter
            ->expects($this->once())
            ->method('write')
            ->with(
                $this->logicalAnd(
                    $this->stringContains("'host' => '127.0.0.1',"),
                    $this->logicalNot(
                        $this->stringContains('extConf')
                    )
                )
            );

        $this->task->setLocalFile($this->localFile);
        $this->task->setRemoteFile($this->remoteFile);
        $this->task->addFileWriter($this->fileWriter);

        $this->task->main();
    }
}
?>