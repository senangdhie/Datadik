<?php
namespace Swiftlet\Controllers;
class Index extends \Swiftlet\Controller
{
	protected
		$title = '';

	public function index()
	{
		$errmsg = '';
		$logged = false;
		$posdata = false;
		if(isset($_POST['destroysession'])) {
			$acc = $this->app->getSingleton('acc');
			$acc->dropsession();
			$posdata = true;
		}
		if(isset($_POST['authuser']) && isset($_POST['authpass']) && !empty($_POST['authuser']) && !empty($_POST['authpass']))
		{
			$u = substr($_POST['authuser'],0,60);
			$p = $_POST['authpass'];
			$e = 0;
			$acc = $this->app->getSingleton('acc');
			$log = $acc->login($u,$p,$e);
			if(!$log) {
				$errmsg = 'email dan password yang anda masukan tidak cocok';
				if($acc->is_loged()) {
					$errmsg = 'pengguna ini sedang login ditempat lain';
				}
			}else{
				$logged = true;
			}
			$posdata = true;
		}
		$this->view->set('posdata',$posdata);
		$this->view->set('logged',$logged);
		$this->view->set('msg', $errmsg);
	}
}
?>