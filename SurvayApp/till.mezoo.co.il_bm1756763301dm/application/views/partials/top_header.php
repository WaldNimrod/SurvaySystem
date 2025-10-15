<?php
// כותרת גרפית עליונה אחודה לכל המסכים
?>
<style>
    .site-header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:10px 12px; border:2px dashed #e0f2f1; border-radius:10px; background:linear-gradient(90deg,#f7fffe,#ffffff); margin-bottom:12px; }
    .site-header .left, .site-header .center, .site-header .right { display:flex; align-items:center; gap:10px; }
    .site-header .center { flex:1; justify-content:center; text-align:center; }
    .site-header .brand-rocket { height:46px; width:auto; }
    .site-header .brand-rocket.big { height:64px; }
    .site-header .brand-codetix { height:28px; width:auto; }
    .site-header .title-gradient { height:36px; width:auto; }
    @media print { .site-header { border:0; } }
</style>
<div class="site-header">
    <div class="right">
        <a href="<?php echo fix_link(site_url('welcome/admin')); ?>" title="ראשי">
            <img src="<?php echo base_url('pics/tilint_logo.svg'); ?>" alt="לוגו טיל" class="brand-rocket"/>
        </a>
    </div>
    <div class="center">
        <img src="<?php echo base_url('pics/tilint_logo.svg'); ?>" alt="טיל" class="brand-rocket big"/>
        <img src="<?php echo base_url('pics/title_gradient.svg'); ?>" alt="מערכת סקרים" class="title-gradient"/>
    </div>
    <div class="left">
        <a href="<?php echo fix_link(site_url('welcome/admin')); ?>" title="ראשי">
            <img src="<?php echo base_url('pics/codetix_text.svg'); ?>" alt="Codetix" class="brand-codetix"/>
        </a>
    </div>
</div>

