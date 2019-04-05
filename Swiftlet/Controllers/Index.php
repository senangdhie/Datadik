<?php
namespace Swiftlet\Controllers;
class Index extends \Swiftlet\Controller
{
	protected
		$title = 'Beranda';

	public function index()
	{
		$errmsg = '';
		$logged = false;
		if(isset($_POST['authuser']) && isset($_POST['authpass']) && !empty($_POST['authuser']) && !empty($_POST['authpass']))
		{
			$u = substr($_POST['authuser'],0,60);
			$p = $_POST['authpass'];
			$e = 0;
			$acc = $this->app->getSingleton('acc');
			$log = $acc->login($u,$p,$e,1);
			if(!$log) {
				$errmsg = 'email dan password yang anda masukan tidak cocok';
				if($acc->is_loged()) {
					$errmsg = 'pengguna ini sedang login ditempat lain';
				}
			}else{
				$logged = true;
			}
		}
		$this->view->set('logged',$logged);
		$this->view->set('msg', $errmsg);
	}
}
?>