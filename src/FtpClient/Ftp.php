<?php
namespace FtpClient;

class Ftp{
	
	private $session;
	private $conn;
	
	public function __construct($ftp){
		$conn = $this->connect($ftp->host, $ftp->user, $ftp->pass);
		$this->conn = $conn;
		
		ftp_pasv($conn, $ftp->passive);
	}
	
	private function connect($host, $user, $pass){
		$conn = ftp_connect($host);
	
		$session = ftp_login($conn, $user, $pass);
		if($session === false)
			return false;
		
		return $conn;
	}
	
	public function getFiles($remotePath, $localPath){
		$files = $this->listFiles($remotePath);
		
		if(!is_array($files))
			return false;
		
		//Download all files to local path
		foreach ($files as $f)
		{
			if((strlen($f) - strlen($remotePath))>3 && strpos($f, '.'))
				ftp_get($this->conn, $localPath.'/'.basename($f),$f, FTP_BINARY);	
		}
		return true;
	}
	
	public function moveFiles($files, $pathFrom, $pathTo){
		if($pathFrom != $pathTo){
			//Move processed files to "path_to_move"(client_config.json)
			foreach ($files as $f)
			{
				//Move file
				ftp_rename($this->conn, $pathFrom."/".$f, $pathTo."/".$f);
			}
			return true;
		}
		
		return null;
	}
	
	public function listFiles($path){
		return ftp_nlist($this->conn, $path);
	}
	
	public function close(){
		ftp_close($this->conn);
	}

	public function fileSize($file){
		return ftp_size($this->conn, $file);
	}
}

