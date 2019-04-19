<?php
  require('global-meta.html.php');
  
  if($this->get('posdata')) {
    echo "<script>location.href = '/';</script>";
  }
?>
<link type="text/css" rel="stylesheet" href="/assets/css/front-theme-1.css">
<div class="container-fluid">
	<div class="row no-gutter">
		<div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
		<div class="col-md-8 col-lg-6">
			<div class="login d-flex align-items-center py-5">
				<div class="container">
          <?php if(!empty($sessid)) { ?>
          <div class="row">
            <nav id="nav-home">
              <div class="nav-home-container">
                <ul>
                  <li><a class="btn btn-outline-primary" href="#"><i class="fa fa-wrench"></i><span>Setting</span></a></li>
                  <li><a class="btn btn-outline-primary" href="#"><i class="fa fa-home"></i><span>Sekolah</span></a></li>
                  <li><a class="btn btn-outline-primary" href="#"><i class="fa fa-users"></i><span>Guru</span></a></li>
                </ul>
              </div>
            </nav>
            <h3>Welcome, gracias, wilujeng...</h3>
            <div class="col-md-9 col-lg-8 mx-auto">
              <form method="post" action="">
                <input type="hidden" name="destroysession" value="yes"/>
                <button class="btn btn-lg btn-danger btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Sign Out</button>
              </form>
            </div>
          </div>
          <?php }else{ ?>
          <div class="row">
					<div class="col-md-9 col-lg-8 mx-auto">
					  <h3 class="login-heading mb-4">Akses Pengguna</h3>
					  <form method="post" action="">
						<div class="form-label-group">
						  <input type="email" id="authuser" name="authuser" class="form-control" placeholder="Email address" required autofocus>
						  <label for="authuser">Username</label>
						</div>
						<div class="form-label-group">
						  <input type="password" id="authpass" name="authpass" class="form-control" placeholder="Password" required>
						  <label for="authpass">Password</label>
						</div>
						<div class="custom-control custom-checkbox mb-3">
						  <input type="checkbox" class="custom-control-input" id="rememberme" name="rememberme">
						  <label class="custom-control-label" for="rememberme">Ingatkan disini</label>
						</div>
						<button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Sign in</button>
						<div class="text-center">
						  <a class="small" href="#">Lupa password?</a></div>
					  </form>
					</div>
				  </div>
          <?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	require('global-closer.html.php');
?>