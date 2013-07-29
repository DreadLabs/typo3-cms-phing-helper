<?php
require_once 'phing/Task.php';

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