<?php
	$a = $this->app->getAction();
	$sessid = $this->get('sessid');
	$encses = $this->get('sesenc');
	if($a!='index') {
		require_once('apps.'.$a.'.html.php');
    }else{
		require('global-meta.html.php');
		echo '<script>vhsi = \''.$encses.'\';InitPage(sp);xzc();</script>';
		require('global-closer.html.php');
    }
?>