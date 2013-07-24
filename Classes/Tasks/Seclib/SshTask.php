<?php
class Seclib_SshTask extends SshTask {

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
		$stream = $this->connection->write($this->command);
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