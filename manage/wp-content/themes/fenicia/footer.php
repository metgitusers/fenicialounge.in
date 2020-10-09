<?php

/**

 * The template for displaying the footer

 *

 * Contains the closing of the #content div and all content after

 *

 * @package WordPress

 * @subpackage Twenty_Sixteen

 * @since Twenty Sixteen 1.0

 */

?>





<!-- </div>

</section> -->

<!-- </div>

</div> -->





<?php wp_footer(); ?>

<script type="text/javascript">

	jQuery(function($) {

	$('.datepicker').datepicker({

		minDate:0

	});



		//$('.datepicker').datepicker();

		/*$('#datepicker').on('focusin', function(e){

			return false;

		}).datepicker();*/

		

	

		



	});

</script>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		

		jQuery('.stellarnav').stellarNav({

			theme: 'light'

		});



		jQuery('.nav_plates_inner b').on('click', function(){

			jQuery('.nav_plates_inner b.current').removeClass('current');

			jQuery(this).addClass('current');

			 

		});

	});

	jQuery('.contact_submit .wpcf7-submit').on('click', function (e) {

		jQuery("input[type=text]").blur();

	});

</script>

<script>

	jQuery(function() {

		jQuery('.toggle-nav').click(function() {

        // Calling a function in case you want to expand upon this.

        toggleNav();

    });

	});



	function toggleNav() {

		if (jQuery('#site-wrapper').hasClass('show-nav')) {

        // Do things on Nav Close

        jQuery('#site-wrapper').removeClass('show-nav');

    } else {

        // Do things on Nav Open

        jQuery('#site-wrapper').addClass('show-nav');

    }



    //$('#site-wrapper').toggleClass('show-nav');

}

jQuery(document).keyup(function(e) {

	if (e.keyCode == 27) {

		if (jQuery('#site-wrapper').hasClass('show-nav')) {

            // Assuming you used the function I made from the demo

            toggleNav();

        }

    } 

});

</script>











<script>

wow = new WOW(

	{

	animateClass: 'animated',

	offset:       100

	}

	);

wow.init();

</script>



<script type="text/javascript">

	jQuery(function(){

    var current = location.pathname;

    jQuery('.page_left_menu_listing ul li a').each(function(){

        var $this = jQuery(this);

        // if the current path is like this link, make it active

        if($this.attr('href').indexOf(current) !== -1){

            $this.parent().addClass('active_sidemenu');

        }

    })

})

  

 



</script>

<script>

	jQuery(function(){

		var $gallery = jQuery('.gallery a').simpleLightbox();



		$gallery.on('show.simplelightbox', function(){

			//console.log('Requested for showing');

		})

		.on('shown.simplelightbox', function(){

			//console.log('Shown');

		})

		.on('close.simplelightbox', function(){

			//console.log('Requested for closing');

		})

		.on('closed.simplelightbox', function(){

			//console.log('Closed');

		})

		.on('change.simplelightbox', function(){

			//console.log('Requested for change');

		})

		.on('next.simplelightbox', function(){

			//console.log('Requested for next');

		})

		.on('prev.simplelightbox', function(){

			//console.log('Requested for prev');

		})

		.on('nextImageLoaded.simplelightbox', function(){

			//console.log('Next image loaded');

		})

		.on('prevImageLoaded.simplelightbox', function(){

			//console.log('Prev image loaded');

		})

		.on('changed.simplelightbox', function(){

			//console.log('Image changed');

		})

		.on('nextDone.simplelightbox', function(){

			//console.log('Image changed to next');

		})

		.on('prevDone.simplelightbox', function(){

			//console.log('Image changed to prev');

		})

		.on('error.simplelightbox', function(e){

			//console.log('No image found, go to the next/prev');

			console.log(e);

		});

	});

	jQuery(document).ready(function(){

		var date = new Date();

		var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

		var nextday = new Date(date.getFullYear(), date.getMonth(), date.getDate()+1);

 // jQuery('#bpickdate').datetimepicker({pickTime: false, minDate: nextday, defaultDate: nextday });

 jQuery('#timepicker').datetimepicker({pickDate: false});



        //jQuery('#ppickdate').datetimepicker({pickTime: false, minDate: nextday, defaultDate: nextday});

        jQuery('#timepicker').datetimepicker({pickDate: false});  

    });

     

/*jQuery("#datepicker").datepicker({

    minDate: 0,

     

}); */

</script>

<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		$('form.reservation-form').validate({

            focusInvalid : false,

			rules: {

				rname: {

					required: true,

                

				},



				lname: {

					required: true,



				},

				date:{

					required:true,

				},

				time:{

					required:true,

				},

				rphone:{

					required: true,

					minlength:10,

                    maxlength:15,

                    number: true,

                    text:false

				},

                occasion:{

                	required:true

                },   

				remail: {

					required: true,

					email:true



				},

				subject:{

					required: true,

				},

				message:{

					required: true,

				}



			},



			messages: {

				rname: "First Name can't be empty.",

				lname: "Last Name can't be empty.",

				date:{

					required:"Date can't be empty.",

				},

				time:{

					required:"Time can't be empty.",

				},

				occasion:{

                	required:"Field can't be empty.",

                }, 

				rphone:{

					required:"Telephone Number can't be empty.",

					minlength:"Telephone Number should be between 10 and 15 digits.",

					maxlength:"Telephone Number should be between 10 and 15 digits.",

					number:"Telephone Number is invalid."

				       },



				remail:{

					required:"Email can't be empty.",

				},       

				subject: "Subject Can't be empty.",

				email: "Email Can't be empty.",

				message:"Message Can't be empty." 



			},



			errorElement: "div",

			errorPlacement: function(error, element) {

				element.after(error);

			}



		});

	});

</script>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		$('form.contact-form').validate({

 

			rules: {

				fname: {

					required: true,

                     

				},

				lname: {

					required: true,

                     

				},

				tel:{

					required: true,

					minlength:10,

                    maxlength:15,

                    number: true

				},



				eemail: {

					required: true,

					email:true



				},

				 

				textarea:{

					required: true,

				}



			}, 



			messages: {

				fname: {

					required: "First Name can't be empty."

                     

                       },

				lname: {

					required:"Last Name can't be empty.",

				       },

				tel:{

					required:"Phone Number can't be empty.",

					number:"The telephone number is invalid.",

					minlength:"Phone Number should be between 10 and 15 digits.",

					maxlength:"Phone Number should be between 10 and 15 digits."

				       },

				 

				eemail: {

					required: "Email Can't be empty.",

					email:"The e-mail address entered is invalid."

				        },

				textarea:"Message Can't be empty."



			},



			errorElement: "div",

			errorPlacement: function(error, element) {

				element.after(error);

			}



		});

	});

</script>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		$('form.eventform').validate({

 

			rules: {

				ename: {

					required: true,

                     

				},

				 

				tel:{

					required: true,

					minlength:10,

                    maxlength:15,

                    number: true

				},



				eemail: {

					required: true,

					email:true



				},

				 

				guestquantity:{

					required: true,

				},

				eventtype:{

					required: true,

				}



			},



			messages: {

				ename: {

					required: "Name can't be empty."

                     

                       },

				 

				tel:{

					required:"Phone Number can't be empty.",

					number:"The telephone number is invalid.",

					minlength:"Phone Number should be between 10 and 15 digits.",

					maxlength:"Phone Number should be between 10 and 15 digits."

				       },

				 

				eemail: {

					required: "Email Can't be empty.",

					email:"The e-mail address entered is invalid."

				        },

				guestquantity:{

					required:

					"Number of Guests Can't be empty.",   

				              },



                eventtype:"Type Of Event Can't be empty.",

			},



			errorElement: "div",

			errorPlacement: function(error, element) {

				element.after(error);

			}



		});

	});



jQuery(document).ready(function(){

    jQuery( document ).on( 'focus', ':input', function(){

        jQuery( this ).attr( 'autocomplete', 'off' );



    });

    

    

});

jQuery('.datepicker').keydown(false);    

 jQuery(function() {

  var regExp = /[a-z]/i;

  jQuery('.wpcf7-tel').on('keypress', function(e) {

    var value = String.fromCharCode(e.which) || e.key;



    // No letters

    if (regExp.test(value)) {

      e.preventDefault();

      return false;

    }

    else{

    	return true;

    }

  });

});

</script>



<!--<script src="js/wow.min.js"></script>-->



<script>

jQuery('p').each(function() {

    var $this = jQuery(this);

    if($this.html().replace(/\s|&nbsp;/g, '').length == 0)

        $this.remove();

});

</script>

<script type="text/javascript">

jQuery(window).load(function() {
	setTimeout(function(){ jQuery('.loader').css('display','none') }, 500);
});



 



</script>



</body>

</html>

