<?php
	/*
		Filename : Acc.php
		Modified : 10-09-2018
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
		
		public function login($u,$p,$e,$l)
		{
			//$l = 2; // 1 : sekolah, 2 : dinas, 3 : semua
			$f = "peran_id<10";
			switch($l) {
				case 1:
					$f = "a1.peran_id=10";
					break;
				case 2:
					$f = "sekolah_id IS NULL AND (peran_id<10 OR peran_id>50)";
					break;
				case 3:
					$f = "peran_id>0";
					break;
				case 4:
					$f = "peran_id>10 AND sekolah_id IS NULL";
					break;
				case 5:
					$f = "peran_id<=10";
					break;
				default:
					$f = "peran_id<0";
					break;
			}
			$psw = $this->passhash($p);
			if($this->mdmode) {
				$psw = $p;
			}
			$q = "SELECT pengguna_id,peran_id,nama,lembaga_id,la_id,kode_wilayah,sekolah_id FROM dbo.pengguna WITH (NOLOCK) WHERE soft_delete=0 AND aktif=1 AND $f AND username='$u' AND password='$psw'";
			if($l==1) {
				$q = "SELECT a1.pengguna_id,a1.peran_id,a1.nama,a1.lembaga_id,a1.la_id,a1.kode_wilayah,a1.sekolah_id FROM dbo.pengguna a1 WITH (NOLOCK) INNER JOIN dbo.sekolah a2 WITH (NOLOCK) ON a1.sekolah_id=a2.sekolah_id WHERE a1.soft_delete=0 AND a2.soft_delete=0 AND a1.aktif=1 AND $f AND a1.username='$u' AND a1.password='$psw'";
			}
			$_login = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
			$login = $_login->fetch();
			if(is_array($login) && !empty($login[0]['pengguna_id']))
			{
				$peran = $login[0]['peran_id'];
				$nama = $login[0]['nama'];
				$lembaga = $login[0]['lembaga_id'];
				$la = $login[0]['la_id'];
				$wilayah = $login[0]['kode_wilayah'];
				$sekolah = $login[0]['sekolah_id'];
				$pid = $login[0]['pengguna_id'];
				$this->pengguna_id = $pid;
				$this->sid = $sekolah;
				$this->peran_id = $peran;
				$sessid = '';
				
				$q = "SELECT COUNT(1) AS jum FROM sso.sess_auth WITH (NOLOCK) WHERE pengguna_id='$pid' AND log_out IS NULL AND expired_time>GETDATE()";
				$_rs = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
				$rs = $_rs->fetch();
				$j = $rs[0]['jum'];
				$arr_pass_auth = array(1,6,8,55,56);
				/*
				if($j>1 && $peran==10) {
					$this->is_loged = true;
				}
				*/
				if($this->is_loged) {
					return false;
				}else{
					if(is_null($peran)) {
						return false;
					}else{
						try{
							$_ip = $this->get_ip();
							$_expired = 'DATEADD(mi,60,GETDATE())';
							if($e>0) {
								$_expired = 'DATEADD(yy,1,GETDATE())';
							}
							
							$q = "INSERT INTO sso.sess_auth(sessid,pengguna_id,log_time,refresh_time,expired_time,log_ip) VALUES(NEWID(),'$pid',GETDATE(),GETDATE(),$_expired,'$_ip')";
							$wl = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
							$wl->run(true);
							
							$q = "SELECT TOP 1 sessid FROM sso.sess_auth WITH (NOLOCK) WHERE pengguna_id='$pid' ORDER BY log_time DESC";
							$_rs = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
							$rs = $_rs->fetch();
							$sessid = $rs[0]['sessid'];
							$this->sessid = $rs[0]['sessid'];
							
							$sign = md5($sessid.$pid);
							$q = "UPDATE sso.sess_auth SET sign='$sign' WHERE sessid='$sessid'";
							$wl = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
							$wl->run(true);
							
							$q = "INSERT INTO dbo.log_pengguna(pengguna_id,alamat_ip,keterangan) VALUES('$pid','$_ip','Login berhasil')";
							$wl = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
							$wl->run(true);
						}catch(\Exception $exp){
							echo "<!--".$exp->getMessage()."-->";
						}
						
						$cookVal = array(
							'sessid' => $sessid,
							'pid' => $pid,
							'user' => $u,
							'type' => $peran,
							'nama' => $nama,
							'lembaga' => $lembaga,
							'wilayah' => trim($wilayah),
							'sekolah' => $sekolah,
							'exp' => $e
						);
						$cookVal = base64_encode(json_encode($cookVal));
						$cookVar = $this->app->getConfig('globalVar');
						$cookURI = $this->app->getConfig('rootURI');
						if(setcookie($cookVar,$cookVal,$e,'/',$cookURI,false,false)) {
							if($peran==1) {
								setcookie('manajerdapodik','dontlookit',$e,'/',$cookURI,false,false);
							}
							return true;
						}else{
							return false;
						}
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
					$q = "SELECT CASE WHEN DATEADD(mi,60,refresh_time)>GETDATE() OR expired_time>GETDATE() THEN 1 ELSE 0 END AS masih FROM sso.sess_auth WITH (NOLOCK) WHERE sessid='$sid'";
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
				$this->sid = $cook['sekolah'];
				$this->peran_id = $cook['type'];
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

				$q = "INSERT INTO dbo.log_pengguna(pengguna_id,alamat_ip,keterangan) SELECT TOP 1 pengguna_id,alamat_ip,'Berhasil logout' FROM log_pengguna WITH (NOLOCK) WHERE pengguna_id='$pid' ORDER BY waktu_log DESC";
				$upd = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
				$upd->run(true);

				$q = "UPDATE sso.sess_auth SET log_out=GETDATE() WHERE sessid='$sessid'";
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
