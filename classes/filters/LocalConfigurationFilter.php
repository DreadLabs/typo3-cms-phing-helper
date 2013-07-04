<?php

include_once 'phing/filters/BaseParamFilterReader.php';
include_once 'phing/filters/ChainableReader.php';

class LocalConfigurationFilter extends BaseParamFilterReader implements ChainableReader {

	protected $file = '';

	public function setFile(PhingFile $file) {
		$this->file = $file;
	}

	public function read($len = null) {
		$defaultConfiguration = '';
		$fileReader = new FileReader($this->file);
		$fileReader->readInto($defaultConfiguration);

		$configurationArray = $this->getDefaultConfigArrayComments($defaultConfiguration);

		$out = '';
		$out .= 'configuration:' . chr(10);
		$out .= print_r($configurationArray[0], TRUE) . chr(10);
		$out .= 'comments:' . chr(10);
		$out .= print_r($configurationArray[1], TRUE) . chr(10);

		return $out;
	}

	/**
	 * Make an array of the comments in the t3lib/stddb/DefaultConfiguration.php file
	 *
	 * @param string $string The contents of the t3lib/stddb/DefaultConfiguration.php file
	 * @param array $mainArray
	 * @param array $commentArray
	 * @return array
	 * @todo Define visibility
	 */
	public function getDefaultConfigArrayComments($string, $mainArray = array(), $commentArray = array()) {
		$lines = explode(LF, $string);
		$in = 0;
		$mainKey = '';
		foreach ($lines as $lc) {
			$lc = trim($lc);
			if ($in) {
				if (!strcmp($lc, ');')) {
					$in = 0;
				} else {
					if (preg_match('/["\']([[:alnum:]_-]*)["\'][[:space:]]*=>(.*)/i', $lc, $reg)) {
						preg_match('/,[\\t\\s]*\\/\\/(.*)/i', $reg[2], $creg);
						$theComment = trim($creg[1]);
						if (substr(strtolower(trim($reg[2])), 0, 5) == 'array' && !strcmp($reg[1], strtoupper($reg[1]))) {
							$mainKey = trim($reg[1]);
							$mainArray[$mainKey] = $theComment;
						} elseif ($mainKey) {
							$commentArray[$mainKey][$reg[1]] = $theComment;
						}
					}
				}
			}
			if (!strcmp($lc, 'return array(')) {
				$in = 1;
			}
		}
		return array($mainArray, $commentArray);
	}
}
?>