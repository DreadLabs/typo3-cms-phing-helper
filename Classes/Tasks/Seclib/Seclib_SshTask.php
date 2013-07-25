<?php
class Seclib_SshTask extends Task {

	private $host = "";

	private $port = 22;

	private $methods = null;

	private $username = "";

	private $password = "";

	private $command = "";

	private $pubkeyfile = '';

	private $privkeyfile = '';

	private $privkeyfilepassphrase = '';

	/**
	 * The name of the property to capture (any) output of the command
	 * @var string
	 */
	private $property = "";

	/**
	 * Whether to display the output of the command
	 * @var boolean
	 */
	private $display = true;

	public function setHost($host) {
		$this->host = $host;
	}

	public function getHost() {
		return $this->host;
	}

	public function setPort($port) {
		$this->port = $port;
	}

	public function getPort() {
		return $this->port;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function getPassword() {
		return $this->password;
	}

	/**
	 * Sets the public key file of the user to scp
	 */
	public function setPubkeyfile($pubkeyfile) {
		$this->pubkeyfile = $pubkeyfile;
	}

	/**
	 * Returns the pubkeyfile
	 */
	public function getPubkeyfile() {
		return $this->pubkeyfile;
	}

	/**
	 * Sets the private key file of the user to scp
	 */
	public function setPrivkeyfile($privkeyfile) {
		$this->privkeyfile = $privkeyfile;
	}

	/**
	 * Returns the private keyfile
	 */
	public function getPrivkeyfile() {
		return $this->privkeyfile;
	}

	/**
	 * Sets the private key file passphrase of the user to scp
	 */
	public function setPrivkeyfilepassphrase($privkeyfilepassphrase) {
		$this->privkeyfilepassphrase = $privkeyfilepassphrase;
	}

	/**
	 * Returns the private keyfile passphrase
	 */
	public function getPrivkeyfilepassphrase($privkeyfilepassphrase) {
		return $this->privkeyfilepassphrase;
	}

	public function setCommand($command) {
		$this->command = $command;
	}

	public function getCommand() {
		return $this->command;
	}

	/**
	 * Sets the name of the property to capture (any) output of the command
	 * @param string $property
	 */
	public function setProperty($property) {
		$this->property = $property;
	}

	/**
	 * Sets whether to display the output of the command
	 * @param boolean $display
	 */
	public function setDisplay($display) {
		$this->display = (boolean) $display;
	}

	/**
	 * Creates an Ssh2MethodParam object. Handles the <sshconfig /> nested tag
	 * @return Ssh2MethodParam
	 */
	public function createSshconfig() {
		$this->methods = new Ssh2MethodParam();
		return $this->methods;
	}

	public function init() {
	}

	public function main() {
		$p = $this->getProject();

// 		if (!function_exists('ssh2_connect')) {
		if (FALSE === class_exists('Net_SSH2')) {
			throw new BuildException("To use SshTask, you need to install the phpseclib/phpseclib package.");
		}


		$methods = !empty($this->methods) ? $this->methods->toArray($p) : array();
// 		$this->connection = ssh2_connect($this->host, $this->port, $methods);
		// @todo: methods
		$this->connection = new Net_SSH2($this->host, $this->port);
// 		if (!$this->connection) {
// 			throw new BuildException("Could not establish connection to " . $this->host . ":" . $this->port . "!");
// 		}

		$could_auth = null;
		if ($this->pubkeyfile) {
// 			$could_auth = ssh2_auth_pubkey_file($this->connection, $this->username, $this->pubkeyfile, $this->privkeyfile, $this->privkeyfilepassphrase);

			$key = new Crypt_RSA();
			$key->setPassword($this->privkeyfilepassphrase);
			$key->loadKey($this->privkeyfile);
			$could_auth = $this->connection->login($this->username, $key);
		} else {
// 			$could_auth = ssh2_auth_password($this->connection, $this->username, $this->password);
			$could_auth = $this->connection->login($this->username, $this->password);
		}
		if (!$could_auth) {
			throw new BuildException("Could not authenticate connection!");
		}

// 		$stream = ssh2_exec($this->connection, $this->command);
		$stream = $this->connection->exec($this->command);
		if (!$stream) {
			throw new BuildException("Could not execute command!");
		}

		$this->log("Executing command {$this->command}", Project::MSG_VERBOSE);

// 		stream_set_blocking($stream, true);
// 		$result = stream_get_contents($stream);
		$result = $stream;

// 		if (!strlen($result)) {
// 			$stderr_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
// 			stream_set_blocking($stderr_stream, true);
// 			$result = stream_get_contents($stderr_stream);
// 		}

		if ($this->display) {
			print($result);
		}

		if (!empty($this->property)) {
			$this->project->setProperty($this->property, $result);
		}

// 		fclose($stream);
// 		if (isset($stderr_stream)) {
// 			fclose($stderr_stream);
// 		}
	}
}
?>