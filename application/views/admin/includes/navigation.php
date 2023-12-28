<?php
$navs = [];
$custom_user_type = "user_type";
switch ($user_type) {
	case 'admin':
	case 'staff':
		$navs = $this->TemplateModel->get_user_access_navs();
		break;
	case $custom_user_type:
		$navs = $this->TemplateModel->{"get_{$custom_user_type}_access_navs"}();

	default:
		break;
}
?>
<div class="sidebar">
	<div class="sidebar-background"></div>
	<div class="sidebar-wrapper scrollbar-inner">
		<div class="sidebar-content">
			<?php
			foreach ($navs as $section => $nav_list) {
				$nav_list_access = array_filter($nav_list, function ($page) use ($page_access) {
					return $page_access[$page['config']->access]['view_data'] ?? '0' == '1';
				});
				if ($nav_list_access == []) continue;
			?>

				<ul class="nav">
					<li class="nav-section">
						<span class="sidebar-mini-icon">
							<i class="fa fa-ellipsis-h"></i>
						</span>
						<h4 class="text-section"><?= $section ?></h4>
					</li>
					<?php
					foreach ($nav_list as $ni => $nav) {
						// echo json_encode($page_access[$page['config']->access]['view_data'], JSON_PRETTY_PRINT);
						$view_access = $page_access[$nav['config']->access]['view_data'] ?? "";
						if ($view_access != '1') continue;
						/** @var TemplateConfig */
						$nav_config = $nav['config'];
						$nav_view = $this->TemplateModel->{$nav_config->view_template}(false);
						$nav_links = $nav_view['links'];
						$icon = $nav['icon'];
						// echo json_encode($page_access[$nav['name']], JSON_PRETTY_PRINT);
					?>
						<li class="nav-item">
							<a href="<?= ad_base_url($nav_links['view']) ?>">
								<i class="<?= $icon ?>"></i>
								<p><?= $nav_view['head'] ?></p>
							</a>
							<?php
							if (isset($nav_links['add'])) {
							?>
								<a href="<?= ad_base_url($nav_links['add']) ?>" class="d-none"></a>
							<?php
							}
							if (isset($nav_links['sort'])) {
							?>
								<a href="<?= ad_base_url($nav_links['sort']) ?>" class="d-none"></a>
							<?php
							}
							if (isset($nav_links['export'])) {
							?>
								<a href="<?= ad_base_url($nav_links['export']) ?>" class="d-none"></a>
							<?php
							}
							?>
							<?php
							if (isset($nav['other_urls'])) {
								foreach ($nav['other_urls'] as $other_url) {
							?>
									<a href="<?= ad_base_url($other_url) ?>" class="d-none"></a>
							<?php
								}
							}
							?>
						</li>
					<?php
					}
					?>
				</ul>
			<?php
			}
			?>

			<ul class="nav">
				<li class="nav-section">
					<span class="sidebar-mini-icon">
						<i class="fa fa-ellipsis-h"></i>
					</span>
					<h4 class="text-section">Account</h4>
				</li>
				<li class="nav-item">
					<a href="<?= ad_base_url('home/change_password') ?>">
						<i class="fas fa-key"></i>
						<p>Change Password</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?= ad_base_url('login/logout') ?>">
						<i class="fas fa-sign-out-alt"></i>
						<p>Logout</p>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
