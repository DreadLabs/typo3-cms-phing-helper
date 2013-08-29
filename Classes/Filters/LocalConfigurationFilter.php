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

require_once 'phing/filters/BaseParamFilterReader.php';
require_once 'phing/filters/ChainableReader.php';
require_once 'Utility/TYPO3/InstallTool.php';

/**
 * Transforms the TYPO3 CMS DefaultConfiguration into a Phing property file
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 * @see FilterReader
 * @package Filters
 */
class LocalConfigurationFilter extends BaseParamFilterReader implements ChainableReader
{

    /**
     * property file output line
     *
     * @var string
     */
    protected $outputLine = '# %comment%%newline%%mainKey%.%subKey%=%defaultConfigurationValue%%newline%';

    /**
     * replacement pairs for unresolveable expressions
     *
     * In order to circumvent to include possible large parts of the TYPO3 CMS
     * framework, this stack holds a list of replacement pairs which are replaced
     * before inclusion of the shipped default configuration file.
     *
     * @var array
     */
    protected $unresolveableReplacementPairs = array(
        '\\TYPO3\\CMS\\Core\\Log\\LogLevel::DEBUG' => '7',
        '\\TYPO3\\CMS\\Core\\Log\\LogLevel::WARNING' => 4,
        'PHP_EXTENSIONS_DEFAULT' => "'php,php3,php4,php5,php6,phpsh,inc,phtml'",
        'FILE_DENY_PATTERN_DEFAULT' => "'\\.(php[3-6]?|phpsh|phtml)(\\..*)?$|^\\.htaccess$'",
        'TYPO3_version' => "''",
    );

    /**
     * content of what is known as TYPO3_CONF_VARS
     *
     * @var array
     */
    protected $defaultConfiguration = array();

    /**
     * directory where to save modified input file
     *
     * The default configuration input file gets cleaned up by unresolveable TYPO3
     * CMS framework expressions and is cached into this directory.
     *
     * @var string
     */
    protected $cacheDir = '/tmp';

    /**
     *
     * @var string
     */
    protected $typo3Version = NULL;

    /**
     * InstallTool utility
     *
     * @var InstallTool
     */
    protected $installTool = NULL;

    public function setCacheDir($dir)
    {
        $this->cacheDir = $dir;
    }

    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * sets the TYPO3 version property
     *
     * This getter also updates the self::unresolveableReplacementPairs stack
     *
     * @param string $typo3Version
     * @return void
     */
    public function setTYPO3Version($typo3Version)
    {
        $this->typo3Version = $typo3Version;

        $this->unresolveableReplacementPairs['TYPO3_version'] = "'" . $typo3Version . "'";
    }

    /**
     *
     * @return string
     */
    public function getTYPO3Version()
    {
        return $this->typo3Version;
    }

    public function addInstallTool(InstallTool $installTool = NULL)
    {
        if (NULL === $installTool) {
            $installTool = new InstallTool();
        }

        $this->installTool = $installTool;
    }

    /**
    * Creates a new LocalConfigurationFilter using the passed in
    * Reader for instantiation.
    *
    * @param Reader A Reader object providing the underlying stream. Must not be <code>null</code>.
    *
    * @return Reader A new filter based on this configuration, but filtering the specified reader
    */
    public function chain(Reader $reader)
    {
        $newFilter = new LocalConfigurationFilter($reader);
        $newFilter->setProject($this->getProject());
        $newFilter->setCacheDir($this->getCacheDir());
        $newFilter->setTYPO3Version($this->getTYPO3Version());
        $newFilter->addInstallTool($this->installTool);
        $newFilter->setInitialized(true);

        return $newFilter;
    }

    /**
     * Reads stream, applies property file formatting and returns resulting stream.
     *
     * @return string transformed buffer.
     * @throws BuildException if TYPO3version is not set in unresolveableReplacementPairs
     */
    public function read($len = null)
    {
        if (FALSE === $this->getInitialized()) {
            $this->initialize();
            $this->setInitialized(TRUE);
        }

        $defaultConfiguration = '';

        if (-1 === version_compare($this->getTYPO3Version(), '0.0.0')) {
            throw new BuildException('You must pass a TYPO3 version via the corresponding parameter in your build file!');
        }

        while (($data = $this->in->read($len)) !== -1) {
            $defaultConfiguration .= $data;
        }

        if ('' === $defaultConfiguration) {
            return -1;
        }

        $this->setDefaultConfiguration($defaultConfiguration);

        $comments = $this->installTool->getDefaultConfigArrayComments($defaultConfiguration);

        $out = $this->createPropertyPathsFromCommentArray($comments);

        return $out;
    }

    /**
     * Initializes any parameters
     *
     * This method is only called when this filter is used through a <filterreader> tag in build file.
     *
     * @return void
     */
    private function initialize()
    {
        $params = $this->getParameters();

        if ($params) {
            foreach ($params as $param) {
                $setter = 'set' . str_replace(' ', '', ucwords(str_replace('-', ' ', $param->getName())));
                if (FALSE === method_exists($this, $setter)) {
                    $msg = sprintf('Unknown parameter "%s" for LocalConfigurationFilter!', $param->getName());
                    throw new BuildException($msg);
                }

                call_user_func(array($this, $setter), $param->getValue());
            }
        }

        if (NULL === $this->installTool) {
            $this->addInstallTool();
        }
    }

    /**
     * modifies the given raw content by creating a temp file and resolving some TYPO3 CMS constants
     *
     * @param string $rawContent
     * @return void
     */
    protected function setDefaultConfiguration($rawContent)
    {
        $resolvedContent = strtr($rawContent, $this->unresolveableReplacementPairs);

        $cacheFile = tempnam($this->cacheDir, 'tmp');

        $fh = fopen($cacheFile, 'w');

        fwrite($fh, $resolvedContent);

        fclose($fh);

        $this->defaultConfiguration = include($cacheFile);

        unlink($cacheFile);
    }

    /**
     * creates the property file lines for the given comments array
     *
     * @param array $comments
     * @return string
     */
    public function createPropertyPathsFromCommentArray($comments)
    {
        $out = '';

        foreach ($comments as $mainKey => $subKeys) {
            foreach ($subKeys as $subKey => $comment) {
                // uncommented entries are undocumented & for internal use only (I guess)
                if ('' === $comment) {
                    continue;
                }

                $defaultConfigurationValue = $this->defaultConfiguration[$mainKey][$subKey];

                // non-scalar values are not available in InstallTool (& also not handled by phing properties)
                if (FALSE === is_scalar($defaultConfigurationValue)) {
                    continue;
                }

                $out .= strtr($this->outputLine, array(
                    '%newline%' => chr(10),
                    '%comment%' => $comment,
                    '%mainKey%' => $mainKey,
                    '%subKey%' => $subKey,
                    '%defaultConfigurationValue%' => $defaultConfigurationValue,
                ));
            }
        }

        return $out;
    }
}
?>