<?php
class Seclib_ScpTask extends Task {

	protected $file = "";

	protected $filesets = array(); // all fileset objects assigned to this task

	protected $todir = "";

	protected $mode = null;

	protected $host = "";

	protected $port = 22;

	protected $methods = null;

	protected $username = "";

	protected $password = "";

	protected $autocreate = true;

	protected $fetch = false;

	protected $localEndpoint = "";

	protected $remoteEndpoint = "";

	protected $pubkeyfile = '';

	protected $privkeyfile = '';

	protected $privkeyfilepassphrase = '';

	protected $connection = null;

	protected $sftp = null;

	protected $count = 0;

	protected $logLevel = Project::MSG_VERBOSE;

	/**
	 * Sets the remote host
	 */
	public function setHost($h) {
		$this->host = $h;
	}

	/**
	 * Returns the remote host
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * Sets the remote host port
	 */
	public function setPort($p) {
		$this->port = $p;
	}

	/**
	 * Returns the remote host port
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * Sets the mode value
	 */
	public function setMode($value) {
		$this->mode = $value;
	}

	/**
	 * Returns the mode value
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * Sets the username of the user to scp
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * Returns the username
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Sets the password of the user to scp
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * Returns the password
	 */
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

	/**
	 * Sets whether to autocreate remote directories
	 */
	public function setAutocreate($autocreate) {
		$this->autocreate = (bool) $autocreate;
	}

	/**
	 * Returns whether to autocreate remote directories
	 */
	public function getAutocreate() {
		return $this->autocreate;
	}

	/**
	 * Set destination directory
	 */
	public function setTodir($todir) {
		$this->todir = $todir;
	}

	/**
	 * Returns the destination directory
	 */
	public function getTodir() {
		return $this->todir;
	}

	/**
	 * Sets local filename
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * Returns local filename
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * Sets whether to send (default) or fetch files
	 */
	public function setFetch($fetch) {
		$this->fetch = (bool) $fetch;
	}

	/**
	 * Returns whether to send (default) or fetch files
	 */
	public function getFetch() {
		return $this->fetch;
	}

	/**
	 * Nested creator, creates a FileSet for this task
	 *
	 * @return FileSet The created fileset object
	 */
	public function createFileSet() {
		$num = array_push($this->filesets, new FileSet());
		return $this->filesets[$num-1];
	}

	/**
	 * Creates an Ssh2MethodParam object. Handles the <sshconfig /> nested tag
	 * @return Ssh2MethodParam
	 */
	public function createSshconfig() {
		$this->methods = new Ssh2MethodParam();
		return $this->methods;
	}

	/**
	 * Set level of log messages generated (default = verbose)
	 * @param string $level
	 */
	public function setLevel($level) {
		switch ($level) {
			case "error": $this->logLevel = Project::MSG_ERR; break;
			case "warning": $this->logLevel = Project::MSG_WARN; break;
			case "info": $this->logLevel = Project::MSG_INFO; break;
			case "verbose": $this->logLevel = Project::MSG_VERBOSE; break;
			case "debug": $this->logLevel = Project::MSG_DEBUG; break;
		}
	}

	public function init() {
	}

	public function main() {
		$p = $this->getProject();

		if (FALSE === class_exists('Net_SSH2')) {
			throw new BuildException("To use ScpTask, you need to install the package phpseclib/phpscelib.");
		}

		if ($this->file == "" && empty($this->filesets)) {
			throw new BuildException("Missing either a nested fileset or attribute 'file'");
		}

		if ($this->host == "" || $this->username == "") {
			throw new BuildException("Attribute 'host' and 'username' must be set");
		}

		$methods = !empty($this->methods) ? $this->methods->toArray($p) : array();
		// @todo: methods...
// 		$this->connection = ssh2_connect($this->host, $this->port, $methods);
		$this->connection = new Net_SSH2($this->host, $this->port);
// 		if (!$this->connection) {
// 			throw new BuildException("Could not establish connection to " . $this->host . ":" . $this->port . "!");
// 		}

		$could_auth = null;
		if ( $this->pubkeyfile ) {
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

		// prepare sftp resource
		if ($this->autocreate) {
// 			$this->sftp = ssh2_sftp($this->connection);
			$this->sftp = new Net_SFTP($this->host, $this->port);
		}

		if ($this->file != "") {
			$this->copyFile($this->file, basename($this->file));
		} else {
			if ($this->fetch) {
				throw new BuildException("Unable to use filesets to retrieve files from remote server");
			}

			foreach($this->filesets as $fs) {
				$ds = $fs->getDirectoryScanner($this->project);
				$files = $ds->getIncludedFiles();
				$dir = $fs->getDir($this->project)->getPath();
				foreach($files as $file) {
					$path = $dir . DIRECTORY_SEPARATOR . $file;

					// Translate any Windows paths
					$this->copyFile($path, strtr($file, '\\', '/'));
				}
			}
		}

		$this->log("Copied " . $this->counter . " file(s) " . ($this->fetch ? "from" : "to") . " '" . $this->host . "'");

		// explicitly close ssh connection
// 		@ssh2_exec($this->connection, 'exit');
		$this->connection->disconnect();
	}

	protected function copyFile($local, $remote) {
		$path = rtrim($this->todir, "/") . "/";

		$scp = new Net_SCP($this->connection);

		if ($this->fetch) {
			$localEndpoint = $path . $remote;
			$remoteEndpoint = $local;

			$this->log('Will fetch ' . $remoteEndpoint . ' to ' . $localEndpoint, $this->logLevel);

// 			$ret = @ssh2_scp_recv($this->connection, $remoteEndpoint, $localEndpoint);
			$ret = $scp->get($remoteEndpoint, $localEndpoint);

			if ($ret === false) {
				throw new BuildException("Could not fetch remote file '" . $remoteEndpoint . "'");
			}
		} else {
			$localEndpoint = $local;
			$remoteEndpoint = $path . $remote;

			if ($this->autocreate) {
// 				ssh2_sftp_mkdir($this->sftp, dirname($remoteEndpoint), (is_null($this->mode) ? 0777 : $this->mode), true);
				$this->sftp->mkdir(dirname($remoteEndpoint), (is_null($this->mode) ? 0777 : $this->mode), true);
			}

			$this->log('Will copy ' . $localEndpoint . ' to ' . $remoteEndpoint, $this->logLevel);

			if (!is_null($this->mode)) {
// 				$ret = @ssh2_scp_send($this->connection, $localEndpoint, $remoteEndpoint, $this->mode);
				// no mode support, is 0644 by default
				$ret = $scp->put($remoteEndpoint, $localEndpoint, NET_SFTP_LOCAL_FILE);
			} else {
// 				$ret = @ssh2_scp_send($this->connection, $localEndpoint, $remoteEndpoint);
				$ret = $scp->put($remoteEndpoint, $localEndpoint, NET_SFTP_LOCAL_FILE);
			}

			if ($ret === false) {
				throw new BuildException("Could not create remote file '" . $remoteEndpoint . "'");
			}
		}

		$this->counter++;
	}
}
?>