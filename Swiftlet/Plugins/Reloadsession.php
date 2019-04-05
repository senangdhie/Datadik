<?php
namespace Swiftlet\Plugins;

class Reloadsession extends \Swiftlet\Plugin
{
	public function actionBefore()
	{
		$acc = $this->app->getSingleton('acc');
		$rtn = $acc->refreshlog();
        $this->view->set('sessioninfo', $rtn);

		$cookVar = $this->app->getConfig('globalVar');
		$cook = array();
		if(isset($_COOKIE[$cookVar]) && !empty($_COOKIE[$cookVar]))
		{
			$cook = json_decode(base64_decode($_COOKIE[$cookVar]),true);
		}
		$this->view->set('cook', $cook);
	}

	public function actionAfter()
	{
		$muid = $this->view->get('muid');
		$q = "SELECT role_id FROM app.module_role WHERE module_id='$muid'";
		$_rs = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
		$rs = $_rs->fetch();
		$mrole = '';
		if(is_array($rs) && !empty($rs[0]['role_id'])) {
			$cookVar = $this->app->getConfig('globalVar');
			if(isset($_COOKIE[$cookVar]) && !empty($_COOKIE[$cookVar]))
			{
				$cook = json_decode(base64_decode($_COOKIE[$cookVar]),true);
				$peran_id = $cook['type'];
				$pengguna_id = $cook['pid'];
				$q = "SELECT role_id FROM app.module_role WHERE module_id='$muid' AND (peran_id=$peran_id OR pengguna_id='$pengguna_id')";
				$_rs = $this->app->getSingleton('upsertlink')->rawQuery($q,0);
				$rs = $_rs->fetch();
				if(is_array($rs) && !empty($rs[0]['role_id'])) {
					$mrole = 'valid';
				}else{
					$mrole = 'invalid';
				}
			}
		}else{
			$mrole = 'public';
		}
		$this->view->set('modulerole', $mrole);
	}
}
?>