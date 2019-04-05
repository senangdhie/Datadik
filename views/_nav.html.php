			<div class="android-header mdl-layout__header mdl-layout__header--waterfall">
				<div class="mdl-layout__header-row">
					<span class="android-title mdl-layout-title">
						<img class="android-logo-image" src="/assets/images/main-logo.png">
					</span>
					<div class="android-header-spacer mdl-layout-spacer"></div>
					<div class="android-search-box mdl-textfield mdl-js-textfield mdl-textfield--expandable mdl-textfield--floating-label mdl-textfield--align-right mdl-textfield--full-width">
						<label class="mdl-button mdl-js-button mdl-button--icon" for="search-field">
							<i class="material-icons">search</i>
						</label>
						<div class="mdl-textfield__expandable-holder">
							<input class="mdl-textfield__input" type="text" id="search-field">
						</div>
					</div>
					<div class="android-navigation-container">
						<nav class="android-navigation mdl-navigation">
							<a class="mdl-navigation__link mdl-typography--text-uppercase" href="/">Beranda</a>
							<a class="mdl-navigation__link mdl-typography--text-uppercase" href="/#/data">Data</a>
							<a class="mdl-navigation__link mdl-typography--text-uppercase" href="/#/info">Informasi</a>
							<a class="mdl-navigation__link mdl-typography--text-uppercase" href="/#/galeri">Galeri</a>
							<a class="mdl-navigation__link mdl-typography--text-uppercase" href="/#/unduh">Unduhan</a>
						</nav>
					</div>
					<span class="android-mobile-title mdl-layout-title">
						<img class="android-logo-image" src="/assets/images/main-logo.png">
					</span>
					<?php if(!empty($sessid) || $this->get('logged')) { ?>
					<button class="android-more-button mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect" id="profile-button">
						<i class="material-icons">account_circle</i>
					</button>
					<ul class="mdl-menu mdl-js-menu mdl-menu--bottom-right mdl-js-ripple-effect" for="profile-button">
						<li class="mdl-menu__item">Profile Saya</li>
						<li class="mdl-menu__item"><a href="/sp/#/logout">Logout</a></li>
					</ul>
					<?php } ?>
					<button class="android-more-button mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect" id="more-button">
						<i class="material-icons">more_vert</i>
					</button>
					<ul class="mdl-menu mdl-js-menu mdl-menu--bottom-right mdl-js-ripple-effect" for="more-button">
						<li class="mdl-menu__item">Login Guru dan Tendik</li>
						<li class="mdl-menu__item">Login Manajemen</li>
					</ul>
				</div>
			</div>