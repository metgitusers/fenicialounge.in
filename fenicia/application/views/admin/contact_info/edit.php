<style>
  .textarea{
   resize: none;
    
}
</style>
<div class="main-content">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Basic form layout section start -->
      <section id="basic-form-layouts">
        <!--<div class="row">
          <div class="col-sm-12">
            <h2 class="content-header">Driver Master</h2>
          </div>
        </div>-->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="page-title-wrap">
                  <h4 class="card-title">Contact Info</h4>
                   <?php if ($this->session->flashdata('success_msg')) : ?>
                        <div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                          <?php echo $this->session->flashdata('success_msg') ?>
                        </div>
                    <?php endif ?>
                    <?php if ($this->session->flashdata('error_msg')) : ?>
                        <div class="alert alert-danger">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                          <?php echo $this->session->flashdata('error_msg') ?>
                        </div>
                    <?php endif ?>
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">

                <!---->
                       <form id="contact_info" class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/contactinfo/contact_info_update" >
                        <div class="form-body">
                          <div class="row">                           
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Email <sup>*</sup></label>
                                <input type="text"  class="form-control"  onkeypress="nospaces(this)" onkeyup="nospaces(this)" required="" id="email"  VALUE="<?php echo $contact_info['email'];?>" name="email">
                              </div>
                            </div>
                         </div>

                          <div class="row">                           
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Phone <sup>*</sup></label>
                                <input type="text"  id="phone" class="form-control"  onkeypress="nospaces(this)" onkeyup="nospaces(this)" required=""  VALUE="<?php echo $contact_info['phone'];?>" name="phone">
                              </div>
                            </div>
                         </div>

                         <div class="row">                           
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Land Line <sup>*</sup></label>
                                <input type="text"  id="land_line" class="form-control"  onkeypress="nospaces(this)" onkeyup="nospaces(this)" required=""  VALUE="<?php echo $contact_info['land_line'];?>" name="land_line">
                              </div>
                            </div>
                         </div>

                       <div class="row">                           
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Address <sup>*</sup></label>
                                 <textarea id="address" name="address" class="form-control" ><?php echo $contact_info['address'];?></textarea>
                            </div>
                         </div>
                       </div>
                        <div class="row">  
                         <div class="form-actions">
                         <input type="hidden" id="id" name="id" value="<?php echo  $contact_info['id']; ?>">
                          <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/contactinfo'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                          <button type="submit" class="btn btn-success">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                          </button>
                        </div>
                      </div>
                    </div>




                        </div>
                     </form>
                <!---->
                </div>
              </div>
            </div>
          </div>


        </div>
      </section>
      <!-- // Basic form layout section end -->
    </div>
  </div>
</div>
<script type="text/javascript">

function nospaces(t){
    if(t.value.match(/\s/g) && t.value.length == 1){
        alert('Sorry, you are not allowed to enter any spaces in the starting.');

        t.value=t.value.replace(/\s/g,'');
    }
}
</script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
  ////////////////////////////////contact info form validation////////////////////////////////////////////
$( "#contact_info" ).validate({
          rules: {
            
           
            email: {
                  required: true,
                  email: true
                },
           
            phone: {
              required: true,
             // digits: true,
             // minlength: 10
              },
            land_line: {
              required: true,
             // digits: true,
             // minlength: 8
              },

            address: "required",
                                      
          },
          messages: {
           
            email: {
                required: "Email address is required",
                email: "Email address must be in the format of name@domain.com"
                  },

            phone: {
              required: "Phone no required",
           // digits: "Enter valid phone no",
           // minlength: "valid phone no required!"
                },
            land_line: {
              required: "Land line no required",
             // digits: "Enter valid phone no",
             // minlength: "valid phone no required!"
                },
            address: "Address is required", 
           
          },
          errorElement: "em",
          /*errorPlacement: function ( error, element ) {
            // Add the `help-block` class to the error element
            error.addClass( "help-block" );

            if ( element.prop( "type" ) === "checkbox" ) {
              error.insertAfter( element.parent( "label" ) );
            } else {
              error.insertAfter( element );
            }
          },*/
          highlight: function ( element, errorClass, validClass ) {
            $( element ).parents( ".form-control" ).addClass( "has-error" ).removeClass( "has-success" );
          },
          unhighlight: function (element, errorClass, validClass) {
            $( element ).parents( ".form-control" ).addClass( "has-success" ).removeClass( "has-error" );
          }
        });
</script>

