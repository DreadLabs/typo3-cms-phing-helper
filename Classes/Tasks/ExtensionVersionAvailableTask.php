<?php
/**
 * ExtensionVersionAvailableTask allows to compare installed extension version
 *
 * Example
 *
 * <taskdef name="extensionversionavailable" class="Tasks.ExtensionVersionAvailableTask" />
 *
 * <extensionversionavailable property="isCurrentSSH2" extension="ssh2" version="0.12" comparison="&gt;=" />
 */
class ExtensionVersionAvailableTask extends Task {

	protected $property = NULL;

	protected $value = TRUE;

	protected $extension = '';

	protected $version = NULL;

	protected $comparison = '==';

	/**
	 *
	 * @var ReflectionExtension
	 */
	protected $reflectionExtension = NULL;

	public function setProperty($property) {
		$this->property = $property;
	}

	public function getProperty() {
		return $this->property;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}

	public function setExtension($extension) {
		$this->extension = $extension;
	}

	public function getExtension() {
		return $this->extension;
	}

	public function setVersion($version) {
		$this->version = $version;
	}

	public function getVersion() {
		return $this->version;
	}

	public function setComparison($comparison) {
		$this->comparison = $comparison;
	}

	public function getComparison() {
		return $this->comparison;
	}

	public function addReflectionExtension(ReflectionExtension $reflectionExtension = NULL) {
		if (NULL === $reflectionExtension) {
			$reflectionExtension = new ReflectionExtension($this->extension);
		}

		$this->reflectionExtension = $reflectionExtension;
	}

	public function main() {
		if (NULL === $this->property) {
			throw new BuildException('You must set the property attribute!');
		}

		if ('' === $this->extension) {
			throw new BuildException('You must set the extension attribute!');
		}

		if (NULL === $this->version) {
			throw new BuildException('You must set the version attribute!');
		}

		if ($this->evaluate()) {
			$this->project->setProperty($this->property, $this->value);
		}
	}

	protected function evaluate() {
		if (FALSE === extension_loaded($this->extension)) {
				$this->log("Unable to load extension " . $this->extension . " to set property " . $this->property, Project::MSG_VERBOSE);
				return FALSE;
		}

		if (NULL === $this->reflectionExtension) {
			$this->addReflectionExtension();
		}
		$extensionVersion = $this->reflectionExtension->getVersion();

		if (FALSE === version_compare($extensionVersion, $this->version, $this->comparison)) {
			$this->log('Extension `' . $this->extension . '` version "' . $extensionVersion . '" is not `' . $this->comparison . '` "' . $this->version . '"');
			return FALSE;
		}

		return TRUE;
	}
}
?>