<div class="sidebar">
	<div class="sidebar-background"></div>
	<div class="sidebar-wrapper scrollbar-inner">
		<div class="sidebar-content">
			<ul class="nav">
				<li class="nav-item">
					<a href="<?= ad_base_url('home/dashboard') ?>">
						<i class="fas fa-home"></i>
						<p>Dashboard</p>
					</a>
				</li>
				<li class="nav-section d-none">
					<span class="sidebar-mini-icon">
						<i class="fa fa-ellipsis-h"></i>
					</span>
					<h4 class="text-section">Masters</h4>
				</li>
				<li class="nav-item d-none">
					<a href="<?= ad_base_url('masters/profile') ?>">
						<i class="fas fa-user"></i>
						<p>Profile</p>
					</a>
				</li>

				<li class="nav-item d-none">
					<a data-toggle="collapse" href="#users-menu">
						<i class="fas fa-users"></i>
						<p>Users</p>
						<span class="caret"></span>
					</a>
					<div class="collapse" id="users-menu">
						<ul class="nav nav-collapse">
							<li>
								<a href="<?= ad_base_url('masters/users/view_roles') ?>">
									<span class="sub-item">Role</span>
								</a>
								<a href="<?= ad_base_url('masters/users/add_role') ?>" class="d-none"></a>
							</li>
							<li>
								<a href="<?= ad_base_url('masters/users/view') ?>">
									<span class="sub-item">Users</span>
								</a>
								<a href="<?= ad_base_url('masters/users/add') ?>" class="d-none"></a>
							</li>
						</ul>
					</div>
				</li>
				<li class="nav-section">
					<span class="sidebar-mini-icon">
						<i class="fa fa-ellipsis-h"></i>
					</span>
					<h4 class="text-section">Website</h4>
				</li>
				<li class="nav-item">
					<a href="<?= ad_base_url('website/view_banners') ?>">
						<i class="fas fa-image"></i>
						<p>Banner</p>
					</a>
					<a href="<?= ad_base_url('website/add_banner') ?>" class="d-none"></a>
				</li>
			</ul>
			<ul class="nav">
				<li class="nav-section">
					<span class="sidebar-mini-icon">
						<i class="fa fa-ellipsis-h"></i>
					</span>
					<h4 class="text-section">Config</h4>
				</li>
				<li class="nav-item">
					<a href="<?= ad_base_url('home/email_config') ?>">
						<i class="fas fa-user-cog"></i>
						<p>Email</p>
					</a>
				</li>
				<li class="nav-item d-none">
					<a href="<?= ad_base_url('home/payment_config') ?>">
						<i class="fas fa-key"></i>
						<p>Payment Gateway</p>
					</a>
				</li>
			</ul>
			<ul class="nav">
				<li class="nav-item resp-visible">
					<a href="<?= ad_base_url('login/logout') ?>">
						<i class="fas fa-sign-out-alt"></i>
						<p>Logout</p>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
