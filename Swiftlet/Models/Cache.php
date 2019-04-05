<?php
namespace Swiftlet\Models;
class Cache extends \Swiftlet\Model
{
	private
		$cache_url = '',
		$cache_time = 3600,
		$cache_user = false;

	public function __construct(\Swiftlet\Interfaces\App $app)
	{
		parent::__construct($app);
	}


	public function cache_time($hour_cache=1)
	{
		if(is_numeric($hour_cache))
		{
			$this->cache_time = $hour_cache * 3600;
		}
	}
	
	public function cache_url($url)
	{
		$this->cache_url = $url;
	}

	public function cache_user($sts)
	{
		$this->cache_user = $sts;
	}

	public function get_time()
	{
		$cache_file = $this->get_cache_url();
		if(file_exists($cache_file))
		{
			//$ftm = date_format(@filemtime(utf8_decode($cache_file)),'d-m-Y H:i:s');
			$ftm = date ('d-m-Y H:i:s', @filemtime(utf8_decode($cache_file)));
			return $ftm;
		}else{
			return false;
		}
	}
	
	public function on_cache()
	{
		$cache_file = $this->get_cache_url();
		$cache_time =$this->cache_time;
		if(file_exists($cache_file))
		{
			$filemtime = @filemtime(utf8_decode($cache_file));
			if(!$filemtime or (time() - $filemtime >= $this->cache_time)) {
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}

	public function write_cache($ctn)
	{
		$cache_file = $this->get_cache_url();
		$ctn = html_entity_decode(html_entity_decode($ctn));
		try {
			if($this->cache_user) {
				$uri = $this->cache_url;
				$_dir = str_replace($uri.'.txt','',$cache_file);
				if (!is_dir($_dir)) {
					@mkdir($_dir);
				}
			}
			@file_put_contents($cache_file,$ctn);
		}catch(Exception $e) {
			die($e->getMessage());
		}
	}

	public function read_cache()
	{
		$cache_file = $this->get_cache_url();
		try {
			@readfile($cache_file);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function get_cache()
	{
		$cache_file = $this->get_cache_url();
		try {
			return file_get_contents($cache_file);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function cached_time()
	{
		$cache_file = $this->get_cache_url();
		try {
			$tm = date("d-m-Y H:i:s.", filemtime($cache_file));
			return $tm;
		}catch (Exception $e) {
			return false;
		}
	}
	
	public function drop_cache()
	{
		$cache_file = $this->get_cache_url();
		try {
			if(file_exists($cache_file)) {
				@unlink($cache_file);
			}
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	private function get_cache_url()
	{
		$conf = $this->app->getConfig('cacheDir');
		if(!empty($this->cache_url))
		{
			$cu = $this->cache_url;
			if($this->cache_user) {
				$cook = $this->getCook();
				$pid = $cook['pid'];
				$cu = $pid.'/'.$cu;
			}
			$file_name = $conf.'/'.$cu;
			return $file_name.'.txt';
		}else{
			$uri = $_SERVER["REQUEST_URI"];
			$uu = explode('/',$uri);
			$ctrl = $uu[1];
			$act = $this->app->getAction();
			$arg = $this->app->getArgs();
			$file_name = $conf.'/'.$ctrl.'_'.$act;
			if(count($arg)>0) {
				$file_name = $file_name.'_';
				foreach ($arg as $a) {
					$file_name = $file_name.$a;
				}
			}
			return $file_name.'.txt';
		}
	}

	private function getCook()
	{
		$cookVar = $this->app->getConfig('globalVar');
		if(isset($_COOKIE[$cookVar]) && !empty($_COOKIE[$cookVar])) {
			$cook = json_decode(base64_decode($_COOKIE[$cookVar]),true);
			return $cook;
		}else{
			return false;
		}
	}
}
?>