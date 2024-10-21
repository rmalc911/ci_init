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
                <h1 class="about-features-title">Terms & Conditions</h1>
                <p class="section-header-crumbs">
                    <span>Home</span>
                    <span class="crumb-active">Terms & Conditions</span>
                </p>
            </div>
        </div>
    </header>

    <main class="terms-page">
        <section class="terms-content markup-content container section-gap"><?= $tnc_content ?></section>
    </main>

    <?= $this->load->view('site/includes/footer', [], true) ?>

    <?= $this->load->view('site/includes/scripts', [], true) ?>

</body>

</html>
