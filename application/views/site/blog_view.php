<?php

/** @var \db\blogs $blog */
/** @var \db\blogs[] $blogs */

$blog_title = $blog->blog_title;
$blog_img = base_url(BLOG_IMAGE_UPLOAD_PATH . $blog->blog_image);
$blog_url = base_url("blogs/" . $blog->blog_url_title);
$page_title = $blog_title . ' | ' . CLIENT_NAME;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?= $this->load->view('site/includes/common-meta', ['page_title' => $page_title], true) ?>
	<link rel="stylesheet" href="<?= as_base_url('css/pages.css?v=' . css_version()) ?>">

	<!-- Facebook Meta Tags -->
	<meta property="og:title" content="<?= $blog_title ?>">
	<meta property="og:description" content="<?= substr($blog->blog_content_preview, 0, 150) ?>">
	<meta property="og:image" content="<?= $blog_img ?>">
	<meta property="og:url" content="<?= $blog_url ?>">
	<meta property="og:site_name" content="<?= $contact_data['business_name'] ?? CLIENT_NAME ?>">
	<meta property="og:type" content="article">

	<!-- Twitter Meta Tags -->
	<meta name="twitter:card" content="summary_large_image">
	<meta property="twitter:domain" content="<?= base_url() ?>">
	<meta property="twitter:url" content="<?= $blog_url ?>">
	<meta name="twitter:title" content="<?= $blog_title ?>">
	<meta name="twitter:description" content="<?= substr($blog->blog_content_preview, 0, 150) ?>">
	<meta name="twitter:image" content="<?= $blog_img ?>">
</head>

<body>
	<?= $this->load->view('site/includes/header', [], true) ?>

	<header class="header-banner">
		<div class="container">
			<div class="section-header">
				<h1 class="about-features-title"><?= $blog_title ?></h1>
				<p class="section-header-crumbs">
					<span>Home</span>
					<span>Blogs</span>
					<span class="crumb-active"><?= $blog_title ?></span>
				</p>
			</div>
		</div>
	</header>

	<main class="site-page blogs-page">
		<section class="page-section blogs-page-section">
			<div class="container">
				<div class="blog-page">
					<article class="blog-main">
						<?php
						?>
						<h1 class="blog-title"><?= $blog_title ?></h1>
						<p class="blog-meta"><?= user_date_d($blog->blog_date) ?></p>
						<div class="blog-content">
							<div class="blog-main-img">
								<img src="<?= $blog_img ?>" alt="<?= htmlspecialchars($blog_title) ?>">
							</div>
							<section class="blog-main-content markup-content"><?= $blog->blog_content ?></section>
						</div>
					</article>
					<aside class="blog-extra">
						<div class="blog-share">
							<h3 class="blog-share-title">Share</h3>
							<ul class="blog-share-list">
								<li class="blog-share-item">
									<a href="https://www.facebook.com/sharer/sharer.php?u=<?= base_url('blog') . '/' . $blog->blog_url_title ?>" class="blog-share-link" target="_blank">
										<i class="lab la-facebook"></i>
									</a>
								</li>
								<li class="blog-share-item">
									<a href="https://twitter.com/intent/tweet?url=<?= base_url('blog') . '/' . $blog->blog_url_title ?>" class="blog-share-link" target="_blank">
										<i class="lab la-twitter"></i>
									</a>
								</li>
								<li class="blog-share-item">
									<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= base_url('blog') . '/' . $blog->blog_url_title ?>" class="blog-share-link" target="_blank">
										<i class="lab la-linkedin"></i>
									</a>
								</li>
								<li class="blog-share-item">
									<a href="https://pinterest.com/pin/create/button/?url=<?= base_url('blog') . '/' . $blog->blog_url_title ?>" class="blog-share-link" target="_blank">
										<i class="lab la-pinterest"></i>
									</a>
								</li>
								<!-- Print -->
								<li class="blog-share-item">
									<a href="javascript:window.print()" class="blog-share-link">
										<i class="la la-print"></i>
									</a>
								</li>
							</ul>
						</div>
						<?php
						if (count($blogs) > 0) {
						?>
							<h2 class="blog-list-banner-title">Other Recent Blogs</h2>
							<div class="blog-list">
								<?php
								foreach ($blogs as $bi => $o_blog) {
									$o_blog_url = "blogs/{$o_blog->blog_url_title}";
									$blog_img = BLOG_IMAGE_UPLOAD_PATH . $o_blog->blog_image;
								?>
									<div class="blog-grid-item">
										<a href="<?= base_url($o_blog_url) ?>" class="blog-grid-img">
											<img src="<?= base_url($blog_img) ?>" alt="<?= htmlspecialchars($o_blog->blog_title) ?>">
										</a>
										<div class="blog-grid-content">
											<h3 class="blog-grid-title"><a href="<?= base_url($o_blog_url) ?>"><?= $o_blog->blog_title ?></a></h3>
											<div class="blog-grid-meta">
												<p class="blog-grid-date"><?= user_date_d($o_blog->blog_date) ?></p>
											</div>
										</div>
									</div>
								<?php
								}
								?>
							</div>
						<?php
						}
						?>
					</aside>
				</div>
			</div>
		</section>
	</main>

	<?= $this->load->view('site/includes/footer', [], true) ?>

	<?= $this->load->view('site/includes/scripts', [], true) ?>

</body>

</html>
