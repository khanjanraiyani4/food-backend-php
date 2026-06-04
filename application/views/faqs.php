<?php $this->load->view('header'); ?>

<section class="inner-banner" style="background-image: url('<?php echo base_url();?>assets/front/images/faq-banner.jpg');">
    <div class="container">
        <div class="inner-pages-banner">
            <h1><?php echo $this->lang->line('faqs') ?></h1>
        </div>
    </div>
</section>
<section class="page-wrapper contact-us-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="faq-image">
                    <img src="<?php echo base_url();?>assets/front/images/FAQs-image.svg">
                </div>
            </div>
            <div class="col-md-6">
                <div class="faq-accordian">
                    <?php 
                        if(!empty($result)){
                            foreach($result as $category){
                    ?>
                               <div class="heading-title"><h2 class="accordion-heading"><?php echo $category->name; ?></h2> </div>
                               <div class="accordion" id="accordion_<?php echo $category->entity_id ?>">
                                <?php
                                    $i = 1;
                                    foreach($category->faqs as $faq){ 
                                ?>
                                    <div class="card">
                                        <div class="card-header" id="heading_<?php echo $faq->entity_id ?>">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#<?php echo 'collapse_'.$faq->entity_id ?>" aria-expanded="true" aria-controls="<?php echo 'collapse_'.$faq->entity_id ?>"><?php echo $faq->question; ?></button>
                                            </h2>
                                        </div>
                                        <div id="<?php echo 'collapse_'.$faq->entity_id ?>" class="collapse" aria-labelledby="heading_<?php echo $faq->entity_id ?>" data-parent="#accordion_<?php echo $category->entity_id ?>">
                                            <div class="card-body">
                                                <?php echo $faq->answer; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php $i++; } ?>
                                </div>
                    <?php 
                            } 
                        }else{
                    ?>
                        <h2><u><?php echo $this->lang->line('coming_soon'); ?></u></h2>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->load->view('footer'); ?>
