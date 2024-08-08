<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Setup</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" type="image/x-icon" href="<?= base_url(LOGO_IMG_MIN) ?>">

    <!-- Fonts and icons -->
    <script src="<?= as_base_url('plugins/webfont/webfont.min.js') ?>"></script>
    <script>
        WebFont.load({
            google: {
                "families": ["Open+Sans:300,400,600,700"]
            },
            custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
                urls: ['<?= aa_base_url('theme/css/fonts.css') ?>']
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= as_base_url('plugins/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= aa_base_url('css/main.css?v=') . css_version() ?>">
    <!--   Core JS Files   -->
    <script src="<?= as_base_url('plugins/jquery-3.6.3.min.js') ?>"></script>
    <script src="<?= as_base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <script>
        const BASEURL = '<?= base_url() ?>';
        const ADMIN_PATH = '<?= base_url(ADMIN_PATH) ?>';
    </script>
</head>

<body>
    <main class="py-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <form class="card mb-3" action="<?= ad_base_url('home/run_query') ?>" method="post">
                        <div class="card-body">
                            <h5 class="card-title">SQL</h5>
                            <textarea class="form-control form-control-sm auto-grow pre" name="sql"><?= $create_query ?><?= $mapping_table_create_query ?></textarea>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Run Query</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Migration</h5>
                            <label for="migration-up" class="mt-2 mb-0">Up</label>
                            <textarea class="form-control form-control-sm auto-grow pre" id="migration-up" readonly><?php foreach ($migration_up as $migration) : ?>$this->db->query(<?= "\n" ?>&#9;"<?= join("\n\t", explode("\n", $migration)) ?>"<?= "\n" ?>);<?= "\n" ?><?php endforeach; ?></textarea>
                            <label for="migration-down" class="mt-2 mb-0">Down</label>
                            <textarea class="form-control form-control-sm auto-grow pre" id="migration-down" readonly><?php foreach ($migration_down as $migration) : ?>$this->db->query("<?= join("\n\t", explode("\n", $migration)) ?>");<?= "\n" ?><?php endforeach; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Interface</h5>
                            <textarea class="form-control form-control-sm auto-grow pre" readonly><?= $interface_body ?><?= $mapping_interface ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- jQuery UI -->
    <script src="<?= as_base_url('plugins/jquery-ui-1.12.1.custom/jquery-ui.min.js') ?>"></script>
    <script src="<?= as_base_url('plugins/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') ?>"></script>

    <!-- jQuery Scrollbar -->
    <script src="<?= as_base_url('plugins/jquery-scrollbar/jquery.scrollbar.min.js') ?>"></script>

    <!-- Moment JS -->
    <script src="<?= as_base_url('plugins/moment/moment.min.js') ?>"></script>

    <!-- Datatables -->
    <script src="<?= as_base_url('plugins/datatables/dataTables.min.js') ?>"></script>
    <!-- Bootstrap Toggle -->
    <script src="<?= as_base_url('plugins/bootstrap-toggle/bootstrap-toggle.min.js') ?>"></script>

    <!-- DateTimePicker -->
    <script src="<?= as_base_url('plugins/datepicker/bootstrap-datetimepicker.min.js') ?>"></script>

    <!-- Bootstrap Tagsinput -->
    <script src="<?= as_base_url('plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') ?>"></script>

    <!-- jQuery Validation -->
    <script src="<?= as_base_url('plugins/jquery.validate/jquery.validate.min.js') ?>"></script>

    <!-- Summernote -->
    <script src="<?= as_base_url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= as_base_url('plugins/summernote/summernote-bs4.min.css') ?>">

    <!-- Select2 -->
    <script src="<?= as_base_url('plugins/select2/select2.full.min.js') ?>"></script>

    <!-- Sweet Alert -->
    <script src="<?= as_base_url('plugins/sweetalert/sweetalert2.min.js') ?>"></script>

    <!-- Azzara JS -->
    <script src="<?= aa_base_url('theme/js/ready.js?v=') . js_version() ?>"></script>

    <!-- Pickr -->
    <script src="<?= as_base_url('plugins/pickr/pickr.min.js'); ?>" type="text/javascript"></script>

    <!-- Admin -->
    <script src="<?= aa_base_url('js/main.js?v=') . js_version() ?>"></script>
</body>

</html>
