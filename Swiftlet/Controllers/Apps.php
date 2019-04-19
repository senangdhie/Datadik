<?php
namespace Swiftlet\Controllers;
class Apps extends \Swiftlet\Controller
{
	protected
		$title = 'Terminal';

	public function index()
	{
        $this->view->set('limited',true);
		$acc = $this->app->getSingleton('acc');
		$acc->getSession();
		$sess_id = $acc->get_sessid();
		$sess_enc = $this->str_rot(base64_encode($sess_id));
		$this->view->set('sessid',$sess_id);
		$this->view->set('sesenc',$sess_enc);
    }

    
	public function sampul()
	{
		$this->view->set('limited',true);
		$acc = $this->app->getSingleton('acc');
		$acc->getSession();
		$sess_id = $acc->get_sessid();
		$pengguna_id = $acc->get_pengguna();
		$this->view->set('sessid',$sess_id);
		$vld = false;
		if(!empty($sess_id) && !empty($pengguna_id)) {
			$vld = true;
		}
		$this->view->set('is_valid',$vld);
	}

	public function depan()
	{
	}

    private function clean_quote($str)
	{
		$rtn = str_replace("'","''",$str);
		return $rtn;
	}
	
	private function str_rot($s, $n = 13) {
		static $letters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
		$n = (int)$n % 26;
		if (!$n) return $s;
		if ($n < 0) $n += 26;
		if ($n == 13) return str_rot13($s);
		$rep = substr($letters, $n * 2) . substr($letters, 0, $n * 2);
		return strtr($s, $letters, $rep);
	}
}
?>