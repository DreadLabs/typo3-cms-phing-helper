<?php
class ExtensionVersionAvailableTaskTest extends PHPUnit_Framework_TestCase
{

    protected $project = NULL;

    protected $task = NULL;

    public function setUp()
    {
        $this->project = $this
            ->getMockBuilder('Project')
            ->getMock();

        $this->task = new ExtensionVersionAvailableTask();
        $this->task->setProject($this->project);
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage You must set the property attribute!
     */
    public function buildExceptionIsThrownIfPropertyNotSet()
    {
        $this->task->main();
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage You must set the extension attribute!
     */
    public function buildExceptionIsThrownIfExtensionIsNotSet()
    {
        $this->task->setProperty('isCurrentSSH2');
        $this->task->main();
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage You must set the version attribute!
     */
    public function buildExceptionIsThrownIfVersionIsNotSet()
    {
        $this->task->setProperty('isCurrentSSH2');
        $this->task->setExtension('ssh2');
        $this->task->main();
    }

    /**
     *
     * @test
     */
    public function setPropertyOfProjectIsCalledIfVersionConditionIsMet()
    {
        $this->project
            ->expects($this->once())
            ->method('setProperty')
            ->with(
                $this->equalTo('isCurrentSSH2'),
                $this->equalTo(TRUE)
            );

        $this->task->setProperty('isCurrentSSH2');
        $this->task->setExtension('ssh2');
        $this->task->setVersion('0.0.0');
        $this->task->setComparison('>=');

        $this->task->main();
    }

    /**
     *
     * @test
     */
    public function logObjectOfProjectIsCalledIfVersionConditionIsNotMet()
    {
        $msg = 'Extension `ssh2` version "0.11.2" is not `==` "0.0.0"';

        $this->project
            ->expects($this->once())
            ->method('logObject')
            ->with(
                $this->equalTo($this->task),
                $this->equalTo($msg),
                $this->equalTo(2)
            );

        $this->task->setProperty('isCurrentSSH2');
        $this->task->setExtension('ssh2');
        $this->task->setVersion('0.0.0');
        $this->task->setComparison('==');

        $this->task->main();
    }
}
?>