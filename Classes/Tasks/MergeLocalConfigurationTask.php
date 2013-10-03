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
require_once 'Utility/TYPO3/ArrayUtility.php';
require_once 'Utility/TYPO3/ConfigurationUtility.php';

/**
 * Merges two TYPO3 CMS LocalConfiguration.php files
 *
 * Usage in a phing build file
 *
 * 1. Import via TaskdefTask:
 *
 * <taskdef classname="Tasks.MergeLocalConfigurationTask" name="mergelocalconfiguration" />
 *
 * 2. Call task:
 *
 * <mergelocalconfiguration base="path/to/typo3conf/LocalConfiguration.php" update="path/to/remote/LocalConfiguration.php" />
 *
 * 3. includeEmptyValues
 *
 * Per default the includeEmptyValues is set to TRUE. But you can set this
 * attribute to false and empty values from base configuration file will not
 * override the settings from an update configuration file (which takes precedence).
 *
 * <mergelocalconfiguration base="file.php" update="file2.php" includeemptyvalues="false" />
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 * @package Tasks
 */
class MergeLocalConfigurationTask extends Task
{

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
     * @var boolean
     */
    protected $includeEmptyValues = TRUE;
    /**
     *
     * @var FileWriter
     */
    protected $fileWriter = NULL;

    /**
     *
     * @param PhingFile $base
     * @return void
     */
    public function setBase(PhingFile $base)
    {
        $this->base = $base;
    }

    /**
     *
     * @return PhingFile
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     *
     * @param PhingFile $update
     * @return void
     */
    public function setUpdate(PhingFile $update)
    {
        $this->update = $update;
    }

    /**
     *
     * @return PhingFile
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     *
     * @var boolean $includeEmptyValues
     * @return void
     */
    public function setIncludeEmptyValues($includeEmptyValues)
    {
        $this->includeEmptyValues = $includeEmptyValues;
    }

    /**
     *
     * @return boolean
     */
    public function getIncludeEmptyValues()
    {
        return $this->includeEmptyValues;
    }

    public function addFileWriter(FileWriter $fileWriter = NULL)
    {
        if (NULL === $fileWriter) {
            $fileWriter = new FileWriter($this->base);
        }

        $this->fileWriter = $fileWriter;
    }

    public function main()
    {
        if (NULL === $this->base) {
            throw new BuildException('You must specify the base file!');
        }

        if (NULL === $this->update) {
            throw new BuildException('You must specify the update file!');
        }

        $baseConfiguration = (array) include($this->base->getAbsolutePath());
        $updateConfiguration = (array) include($this->update->getAbsolutePath());

        $mergedConfiguration = ArrayUtility::array_merge_recursive_overrule($baseConfiguration, $updateConfiguration, FALSE, $this->includeEmptyValues);

        $configuration = new ConfigurationUtility($mergedConfiguration);
        $phpCode = $configuration->getLocalConfigurationArray();

        if (NULL === $this->fileWriter) {
            $this->addFileWriter();
        }

        $this->fileWriter->write($phpCode);
        $this->fileWriter->close();
    }
}
?>