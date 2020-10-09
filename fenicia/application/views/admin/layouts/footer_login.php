<!-- jQuery 3 --> 
<script src="<?php echo base_url()?>/public/js/bootstrap.min.js"></script> 
<script src="<?php echo base_url()?>/public/js/jquery.dataTables.min.js"></script> 
<script src="<?php echo base_url()?>/public/js/dataTables.bootstrap.min.js"></script> 
<script src="<?php echo base_url()?>/public/js/adminlte.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url()?>/public/js/scrolltopcontrol.js"></script> 
<script type="text/javascript" src="<?php echo base_url()?>/public/js/script.js"></script> 
<!--calendar--> 
<script type="text/javascript" src="<?php echo base_url()?>/public/js/bootstrap-datepicker.js"></script> 
<!--calendar-->
</body>
</html>
<script>
$(document).ready(function() {     
    // $('a[href="' + location.href + '"]').parents('li,ul').addClass('active');
    // $(".last_menu").niceScroll();  
});
</script>
<?php if($this->session->flashdata('error_msg')){?>
    <script>
        var dialog = bootbox.dialog({
                            message: "<?php echo $this->session->flashdata('error_msg'); ?>",
                            closeButton: true
                        }).css({
                            'margin-top': '50px'
                        });
                        setTimeout(function() {
                            dialog.modal('hide');
                        }, 1000);
    </script>
<?php }?>