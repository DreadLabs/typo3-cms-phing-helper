<?php
class Seclib_ScpTask extends ScpTask {

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

		if ($this->fetch) {
			$localEndpoint = $path . $remote;
			$remoteEndpoint = $local;

			$this->log('Will fetch ' . $remoteEndpoint . ' to ' . $localEndpoint, $this->logLevel);

// 			$ret = @ssh2_scp_recv($this->connection, $remoteEndpoint, $localEndpoint);
			$scp = new Net_SCP($this->connection);
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