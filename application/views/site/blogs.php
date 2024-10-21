<?php

/** @var array<\db\blogs> $blogs */

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?= $this->load->view('site/includes/common-meta', [], true) ?>
	<link rel="stylesheet" href="<?= as_base_url('css/pages.css?v=' . css_version()) ?>">
</head>

<body>
	<?= $this->load->view('site/includes/header', [], true) ?>

	<header class="header-banner">
		<div class="container">
			<div class="section-header">
				<h1 class="about-features-title">Blogs</h1>
				<p class="section-header-crumbs">
					<span>Home</span>
					<span>Blogs</span>
				</p>
			</div>
		</div>
	</header>

	<main class="site-page blogs-page">
		<section class="page-section blogs-page-section">
			<div class="container">
				<div class="blog-grid">
					<?php foreach ($blogs as $bi => $blog) : $blog_url = base_url('blogs') . ('/' . $blog->blog_url_title) ?>
						<div class="blog-grid-item">
							<a href="<?= ($blog_url) ?>" class="blog-grid-img">
								<img src="<?= base_url(BLOG_IMAGE_UPLOAD_PATH . $blog->blog_image) ?>" alt="<?= htmlspecialchars($blog->blog_title) ?>">
							</a>
							<div class="blog-grid-content">
								<h3 class="blog-grid-title"><a href="<?= ($blog_url) ?>"><?= ellipsize($blog->blog_title, 100) ?></a></h3>
								<div class="blog-grid-meta">
									<p class="blog-grid-date"><?= date(user_date, strtotime($blog->blog_date)) ?></p>
								</div>
								<p class="blog-grid-desc"><?= $blog->blog_content_preview ?></p>
							</div>
						</div>
					<?php endforeach; ?>
					<?php
					if ($pagination['total'] == 0) {
					?>
						<div class="blog-list-banner">
							<h2 class="blog-list-banner-title">No blogs available at the moment</h2>
						</div>
					<?php
					}
					?>
				</div>
				<?php
				if ($pagination['total'] > $pagination['per_page']) {
				?>
					<!-- pagination -->
					<div class="pagination-wrapper">
						<ul class="page-pagination">
							<?php
							if ($pagination['page'] > 2) {
							?>
								<li><a class="page-numbers" href="<?= base_url('blogs') . '?page=1' ?>"><span class="fa fa-double-angle-left"></span> First Page</a></li>
							<?php
							}
							if ($pagination['page'] > 1) {
							?>
								<li><a class="page-numbers" href="<?= base_url('blogs') . '?page=' . ($pagination['page'] - 1) ?>"><span class="fa fa-angle-left"></span> Previous Page</a></li>
							<?php
							}
							if ($pagination['total'] > ($pagination['page'] * $pagination['per_page'])) {
							?>
								<li><a class="page-numbers" href="<?= base_url('blogs') . '?page=' . ($pagination['page'] + 1) ?>">Next Page <span class="fa fa-angle-right"></span></a></li>
							<?php
							}
							?>
						</ul>
					</div>
					<!-- //pagination -->
				<?php
				}
				?>
			</div>
		</section>
	</main>

	<?= $this->load->view('site/includes/footer', [], true) ?>

	<?= $this->load->view('site/includes/scripts', [], true) ?>

</body>

</html>
