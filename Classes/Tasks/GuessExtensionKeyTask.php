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

require_once 'phing/Task.php';

/**
 * Guesses a TYPO3 CMS extension key based on the path the extension project resides.
 *
 * Usage
 *
 * <taskdef classname="Tasks.GuessExtensionKeyTask" name="guessextensionkey" />
 * <guessextensionkey from="0" />
 */
class GuessExtensionKeyTask extends Task
{

    protected $property = '';

    protected $strip = 0;

    private $baseDir = NULL;

    public function setProperty($property)
    {
        $this->property = $property;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function setStrip($strip)
    {
        $this->strip = (int) $strip;
    }

    public function getStrip()
    {
        return $this->strip;
    }

    public function addBaseDir(PhingFile $baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function main()
    {
        if ('' === $this->property) {
            throw new BuildException('You must specify the property attribute!');
        }

        if (true === is_null($this->baseDir)) {
            $baseDir = new PhingFile($this->project->getBaseDir());
            $this->addBaseDir($baseDir);
        }

        $directorySeparator = FileSystem::getFileSystem()->getSeparator();

        $baseName = $this->baseDir->getPath();

        $baseNameParts = explode($directorySeparator, $baseName);
        $numberOfBaseNameParts = count($baseNameParts);

        if ($this->strip > $numberOfBaseNameParts) {
            throw new BuildException('Cannot strip ' . $this->strip . ' components from a ' . $numberOfBaseNameParts . ' components path!');
        }

        while ($this->strip > 0) {
            array_pop($baseNameParts);
            $this->strip = $this->strip - 1;
        }

        $baseName = implode($directorySeparator, $baseNameParts);

        $baseName = basename($baseName);

        $this->project->setProperty($this->property, $baseName);
    }
}
?>