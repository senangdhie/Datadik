<?php
	if($this->get('is_valid')) {
        $sessid = $this->get('sessid');
        require_once('_nav.html.php');
?>
	<div class="container" id="vinividivici"></div>
	<script>ReloadContent('blank');</script>
<?php
	}else{
		echo "<script>location.href='/';</script>";
	}
?>