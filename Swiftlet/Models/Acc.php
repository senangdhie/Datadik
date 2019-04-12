<?php
	/*
		Filename : Acc.php
		Modified : 07-04-2019
	*/
	namespace Swiftlet\Models;
	class Acc extends \Swiftlet\Model
	{
		protected
			$sessid = null,
			$pengguna_id = null,
			$sid = null,
			$peran_id = null,
			$is_loged = false;
		private
			$mdmode = false;

		public function __construct(\Swiftlet\Interfaces\App $app)
		{
			parent::__construct($app);
		}

		public function withmd5()
		{
			$this->mdmode = true;
		}
		
		public function login($u,$p,$e)
		{
			$psw = $this->passhash($p);
			if($this->mdmode) {
				$psw = $p;
			}
			$q = "SELECT pengguna_id,nama,kode_wilayah FROM pengguna WHERE aktif=1 AND username='$u' AND password='$psw'";
			$_login = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
			$login = $_login->fetch();
			if(is_array($login) && !empty($login[0]['pengguna_id']))
			{
				$nama = $login[0]['nama'];
				$wilayah = $login[0]['kode_wilayah'];
				$pid = $login[0]['pengguna_id'];
				$this->pengguna_id = $pid;
				$sessid = '';
				
				if($this->is_loged) {
					return false;
				}else{
					try{
						$_ip = $this->get_ip();
						$_expired = "now()+interval '1 hour'";
						if($e>0) {
							$_expired = "now()+interval '1 year'";
						}
						
						$q = "INSERT INTO sso.sess_auth(sessid,pengguna_id,log_time,refresh_time,expired_time,log_ip) VALUES(uuid_generate_v4(),'$pid',now(),now(),$_expired,'$_ip')";
						$wl = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
						$wl->run(true);
						
						$q = "SELECT sessid FROM sso.sess_auth WHERE pengguna_id='$pid' ORDER BY log_time DESC";
						$_rs = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
						$rs = $_rs->fetch();
						$sessid = $rs[0]['sessid'];
						$this->sessid = $rs[0]['sessid'];
						
						$sign = md5($sessid.$pid);
						$q = "UPDATE sso.sess_auth SET sign='$sign' WHERE sessid='$sessid'";
						$wl = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
						$wl->run(true);
						
						$q = "INSERT INTO log_pengguna(pengguna_id,alamat_ip,keterangan) VALUES('$pid','$_ip','Login berhasil')";
						$wl = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
						$wl->run(true);
					}catch(\Exception $exp){
						echo "ERROR Detected.<!--".$exp->getMessage()."-->";
					}
					
					$cookVal = array(
						'sessid' => $sessid,
						'pid' => $pid,
						'user' => $u,
						'nama' => $nama,
						'wilayah' => trim($wilayah),
						'exp' => $e
					);
					$cookVal = base64_encode(json_encode($cookVal));
					$cookVar = $this->app->getConfig('globalVar');
					$cookURI = $this->app->getConfig('rootURI');
					if(setcookie($cookVar,$cookVal,$e,'/',$cookURI,false,false)) {
						return true;
					}else{
						return false;
					}
				}
			}else{
				return false;
			}
		}

		public function refreshlog()
		{
			$cookVar = $this->app->getConfig('globalVar');
			if(isset($_COOKIE[$cookVar])&&!empty($_COOKIE[$cookVar]))
			{
				$cook = json_decode(base64_decode($_COOKIE[$cookVar]),true);
				$sid = $cook['sessid'];
				if(!empty($sid)) {
					$q = "SELECT CASE WHEN now()+interval '1 hour'>now() OR expired_time>now() THEN 1 ELSE 0 END AS masih FROM sso.sess_auth WHERE sessid='$sid'";
					$_rs = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
					$rs = $_rs->fetch();
					$vld = 0;
					if(is_array($rs)) {
						$vld = $rs[0]['masih'];
					}
					if($vld<1) {
						$cookURI = $this->app->getConfig('rootURI');
						setcookie($cookVar,'',time()-3600,'/',$cookURI,false,true);
						return 'expiredsession';
					}else{
						$q = "UPDATE sso.sess_auth SET refresh_time=getdate() WHERE sessid='$sid'";
						$wl = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
						$wl->run(true);
						return '';
					}
				}
			}
		}

		public function authlog($sessid)
		{
			$cookVar = $this->app->getConfig('globalVar');
			if(isset($_COOKIE[$cookVar])&&!empty($_COOKIE[$cookVar]))
			{
				$cook = json_decode(base64_decode($_COOKIE[$cookVar]),true);
				$sid = $cook['sessid'];
				if($sid==$sessid) {
					$_s = $this->refreshlog();
					if(!empty($_s)) {
						return false;
					}else{
						return true;
					}
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		
		public function getSession()
		{
			$cookVar = $this->app->getConfig('globalVar');
			if(isset($_COOKIE[$cookVar])&&!empty($_COOKIE[$cookVar]))
			{
				$this->is_loged = true;
				$cook = json_decode(base64_decode($_COOKIE[$cookVar]),true);
				$this->pengguna_id = $cook['pid'];
				#$this->sid = $cook['sekolah'];
				#$this->peran_id = $cook['type'];
				$this->sessid = $cook['sessid'];
				return true;
			}else{
				return false;
			}
		}
		
		public function dropsession()
		{
			$cookVar = $this->app->getConfig('globalVar');
			if(isset($_COOKIE[$cookVar])&&!empty($_COOKIE[$cookVar]))
			{
				$this->getSession();
				
				$sessid = $this->sessid;
				$pid = $this->pengguna_id;

				$q = "INSERT INTO log_pengguna(pengguna_id,alamat_ip,keterangan) SELECT pengguna_id,alamat_ip,'Berhasil logout' AS keterangan FROM log_pengguna WHERE pengguna_id='$pid' ORDER BY waktu_log DESC LIMIT 1 OFFSET 0";
				$upd = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
				$upd->run(true);

				$q = "UPDATE sso.sess_auth SET log_out=now() WHERE sessid='$sessid'";
				$wl = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
				$wl->run(true);
				$cookURI = $this->app->getConfig('rootURI');
				setcookie($cookVar,'',time()-3600,'/',$cookURI,false,true);
				return true;
			}else{
				return false;
			}
		}

		public function rolemodule()
		{

		}
		
		public function is_loged()
		{
			return $this->is_loged;
		}
		
		public function get_pengguna()
		{
			return $this->pengguna_id;
		}
		
		public function get_sekolah()
		{
			return $this->sid;
		}
		
		public function get_peran()
		{
			return $this->peran_id;
		}
		
		public function get_sessid()
		{
			return $this->sessid;
		}
		
		public function ganti($u,$p0,$p1,$p2)
		{
			$p0 = $this->passhash($p0);
			$p1 = $this->passhash($p1);
			$p2 = $this->passhash($p2);
			$q = "SELECT COUNT(*) AS jum FROM dbo.pengguna WITH (NOLOCK) WHERE aktif=1 AND username='$u' AND password='$p0'";
			$_rs = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
			$rs = $_rs->fetch();
			$jum = $rs[0]['jum'];
			if($jum!=0) {
				try {
					$q = "UPDATE dbo.pengguna SET password='$p1' WHERE aktif=1 AND username='$u' AND password='$p0'";
					$upd = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
					$upd->run(true);
					$upd = $this->app->getSingleton('upsertlink')->rawQuery($q,1);
					$upd->run(true);
					$upd = $this->app->getSingleton('upsertlink')->rawQuery($q,2);
					$upd->run(true);
					return true;
				}catch(\Exception $e){
					echo "<!--".$e->getMessage()."-->";
				}
			}else{
				return false;
			}
		}
		
		public function getLoginLog($u)
		{
			$sql = "SELECT p.pengguna_id,l.waktu_login,l.alamat_ip,l.keterangan FROM dbo.log_pengguna l WITH (NOLOCK) INNER JOIN dbo.pengguna p WITH (NOLOCK) ON l.pengguna_id = p.pengguna_id WHERE p.username='$u' ORDER BY l.waktu_login DESC";
			$_rs = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
			$log = $_rs->fetch();
			return $log;
		}
		
		private function passhash($pass)
		{
			//$_salt = '}#f4ga~g%7hjg4&j(7mk?/!bj30ab-wi=6^7-$^R9F|GK5J#E6WT;IO[JN';
			//return md5(sha1($_salt.$pass));
			return md5($pass);
		}

		private function get_ip()
		{
			$_ip = '0.0.0.0';
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
					$_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
					$_ip = $_SERVER['REMOTE_ADDR'];
			}
			return $_ip;
		}
	}
?>
