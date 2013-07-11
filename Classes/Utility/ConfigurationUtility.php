<?php

include_once 'Utility/ArrayUtility.php';

class ConfigurationUtility {

	/**
	 *
	 * @see typo3/sysext/core/Classes/Configuration/ConfigurationManager.php::writeLocalConfiguration()
	 *
	 * @var string
	 */
	const OUTPUT_TEMPLATE = '<?php%newline%return %localConfigurationArray%;%newline%?>';

	protected $configuration = array();

	public function __construct(array $configuration) {
		$this->configuration = $configuration;
	}

	public function getLocalConfigurationArray() {
		$phpCode = strtr(self::OUTPUT_TEMPLATE, array(
			'%newline%' => chr(10),
			'%localConfigurationArray%' => $this->getExportedLocalConfigurationArray(),
		));

		return $phpCode;
	}

	protected function getExportedLocalConfigurationArray() {
		$sortedConfiguration = ArrayUtility::sortByKeyRecursive($this->configuration);
		$renumberedArray = ArrayUtility::renumberKeysToAvoidLeapsIfKeysAreAllNumeric($sortedConfiguration);

		return ArrayUtility::arrayExport($renumberedArray);
	}
}
?>