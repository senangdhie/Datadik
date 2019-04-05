<?php
	$app->setConfig('siteName', 'Sistem Informasi Pendidikan');
	$app->setConfig('urlRewrite', TRUE);
	$app->setConfig('maintenis', FALSE);
	$app->setConfig('rootPath', '/');
	$app->setConfig('rootURI', '');
	$app->setConfig('globalSess', 'djanTjook');
	$app->setConfig('globalVar', 'djanTjook');
	$app->setConfig('cacheDir', 'E:\www\cache');
	$app->setConfig('dbo',array(
			array('127.0.0.1','datadik','postgres','rahasia')
		)
	);
	$app->setConfig('mail',array(
			array('mail.dapodikmail.id','sys@dapodikmail.id','rahasia',25,0)
		)
	);
?>