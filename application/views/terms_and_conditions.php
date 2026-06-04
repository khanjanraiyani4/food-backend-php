<?php $this->load->view('header'); ?>

<section class="inner-banner" style="background-image: url('<?php echo ($terms_and_conditions[0]->image)?image_url.$terms_and_conditions[0]->image:cms_banner_img?>');">
    <div class="container">
        <div class="inner-pages-banner">
            <h1><?php echo $this->lang->line('terms_and_conditions') ?></h1>
        </div>
    </div>
</section>
<section class="page-wrapper contact-us-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php if (!empty($terms_and_conditions)) { ?>
                    <div class="widgets"><?php echo $terms_and_conditions[0]->description; ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/front/js/scripts/admin-management-front.js"></script>
<?php $this->load->view('footer'); ?>
