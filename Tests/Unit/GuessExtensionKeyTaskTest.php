<?php
class GuessExtensionKeyTaskTest extends PHPUnit_Framework_TestCase
{

    protected $project = NULL;

    protected $task = NULL;

    public function setUp()
    {
        Phing::startup();

        $this->project = $this->getMockBuilder('Project')
            ->setMethods(array('getBasedir'))
            ->getMock();

        $this->task = new GuessExtensionKeyTask();
        $this->task->setProject($this->project);
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage You must specify the property attribute!
     */
    public function throwsExceptionIfPropertyAttributeIsNotSet()
    {
        $this->task->main();
    }

    /**
     *
     * @test
     */
    public function savesTheProjectsBaseDirByDefaultIntoTheGivenProperty()
    {
        $this->project
            ->expects($this->once())
            ->method('getBasedir')
            ->will($this->returnValue('/tmp/myext'));

        $this->task->setProperty('guessed.extension.key');
        $this->task->main();

        $actualGuessedExtensionKey = $this->project->getProperty('guessed.extension.key');
        $expectedExtensionKey = 'myext';

        $this->assertEquals($expectedExtensionKey, $actualGuessedExtensionKey);
    }

    /**
     *
     * @test
     */
    public function savesTheBaseDirRelativeToProjectBaseDirAndStrippedByGivenComponentNumbers()
    {
        $this->project
            ->expects($this->once())
            ->method('getBasedir')
            ->will($this->returnValue('/tmp/yourext/build'));

        $this->task->setProperty('guessed.extension.key');
        $this->task->setStrip(1);
        $this->task->main();

        $actualGuessedExtensionKey = $this->project->getProperty('guessed.extension.key');
        $expectedExtensionKey = 'yourext';

        $this->assertEquals($expectedExtensionKey, $actualGuessedExtensionKey);
    }

    /**
     *
     * @test
     * @expectedException BuildException
     * @expectedExceptionMessage Cannot strip 6 components from a 5 components path!
     */
    public function throwsAnExceptionIfStrippedComponentNumberIsHigherThanActualPathParts()
    {
        $this->project
            ->expects($this->once())
            ->method('getBasedir')
            ->will($this->returnValue('/tmp/foobar/example/build'));

        $this->task->setProperty('my.ext.key');
        $this->task->setStrip(6);
        $this->task->main();
    }
}
?>