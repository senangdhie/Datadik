<?php
	$maintenis = $this->app->getConfig('maintenis');
	$pageTitle = $this->get('pageTitle'); if(!empty($pageTitle)) { $pageTitle .= ' - '; }
	$sesinfo = $this->get('sessioninfo');
	$modulerole = $this->get('modulerole');
	$cook = $this->get('cook');
	$sessid = '';
	if(isset($cook['sessid'])) { $sessid = $cook['sessid']; }
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="description" content="Data pokok pendidikan"><meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"><title><?php echo $this->htmlEncode($pageTitle).$this->htmlEncode($this->app->getConfig('siteName')); ?></title>
<link type="text/css" rel="stylesheet" href="/assets/css/font-awesome.min.css"><link type="text/css" rel="stylesheet" href="/assets/css/bootstrap.css"><link type="text/css" rel="stylesheet" href="/assets/css/custom.min.css">
<?php
	if(!empty($sessid)) { echo "<script>var sid = '$sessid';</script>"; }
?>
<script type="text/javascript" src="/assets/js/jquery.min.js"><script type="text/javascript" src="/assets/js/popper.min.js"></script><script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>

</head><body><?php
		if($_SERVER['SERVER_NAME']!='localhost' && !isset($_SERVER["HTTPS"])) {
			echo "<script>location.href='https://".$_SERVER['SERVER_NAME']."';</script>";
		}
		
		if($maintenis) {
			echo "<script>location.href='/maintenis.html';</script>";
		}else{
			if($this->get('limited') || $modulerole=='invalid' || $sesinfo=='expiredsession') {
				echo "<script>location.href='/';</script>";
			}
		}
	?>
