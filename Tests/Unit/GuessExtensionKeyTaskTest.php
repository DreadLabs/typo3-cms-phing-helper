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
 * Unit tests for GuessExtensionKeyTask
 *
 */
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