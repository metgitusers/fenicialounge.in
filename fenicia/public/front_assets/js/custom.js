// Add , Dlelete row dynamically



//custom editor
  $('.newPost button[data-func]').click(function(){
    document.execCommand( $(this).data('func'), false 	);
  });

  $('.newPost select[data-func]').change(function(){
    var $value = $(this).find(':selected').val();
    document.execCommand( $(this).data('func'), false, $value);
  });

  if(typeof(Storage) !== "undefined") {

  $('.editor').keypress(function(){
    $(this).find('.saved').detach();
  });
    $('.editor').html(localStorage.getItem("wysiwyg")) ;
    
    $('button[data-func="save"]').click(function(){
      $content = $('.editor').html();
      localStorage.setItem("wysiwyg", $content);
      $('.editor').append('<span class="saved"><i class="fa fa-check"></i></span>').fadeIn(function(){
        $(this).find('.saved').fadeOut(500);
      });
    });
    
    $('button[data-func="clear"]').click(function(){
      $('.editor').html('');
      localStorage.removeItem("wysiwyg");
    });
    
  }

//Active Div
/*jQuery('.ad_holder').click(function(){
  jQuery('.ad_holder').removeClass('active');
  jQuery(this).addClass('active');
});*/

//datepicker
$( function() {
	$( "#datepicker" ).datepicker({
		numberOfMonths: 1,
		showButtonPanel: false,
		dateFormat:"dd-mm-yy",
	}).datepicker("setDate", new Date());
} );


//jQuery time MULTISTEP FORM

//jQuery time
var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

$(".next").click(function(){
	if ($("#msform").valid()) {
		$("#msform").css("height", "auto");
		if(animating) return false;
		animating = true;
		
		current_fs = $(this).parent();
		next_fs = $(this).parent().next();
		

		//activate next step on progressbar using the index of next_fs
		$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
		
		//show the next fieldset
		
			next_fs.show();
		
		//hide the current fieldset with style
		current_fs.animate({opacity: 0}, {
			step: function(now, mx) {
				//as the opacity of current_fs reduces to 0 - stored in "now"
				//1. scale current_fs down to 80%
				scale = 1 - (1 - now) * 0.2;
				//2. bring next_fs from the right(50%)
				left = (now * 50)+"%";
				//3. increase opacity of next_fs to 1 as it moves in
				opacity = 1 - now;
				current_fs.css({
	        'transform': 'scale('+scale+')',
	        'position': 'absolute'
	      });
				next_fs.css({'left': left, 'opacity': opacity});
				$("#msform").css("height", $(this).closest("#msform").find("fieldset:visible").height());
			}, 
			duration: 600, 
			complete: function(){
				current_fs.hide();
				animating = false;
				$("#msform").css("height", $(this).closest("#msform").find("fieldset:visible").height());
			}, 
			//this comes from the custom easing plugin
			easing: 'easeInOutBack'
		});
	}
});

$(".previous").click(function(){	
	$("#msform").css("height", "auto");
	if(animating) return false;
	animating = true;
	
	current_fs = $(this).parent();
	previous_fs = $(this).parent().prev();
	
	//de-activate current step on progressbar
	$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
	
	//show the previous fieldset
	previous_fs.show(); 
	//hide the current fieldset with style
	current_fs.animate({opacity: 0}, {
		step: function(now, mx) {
			//as the opacity of current_fs reduces to 0 - stored in "now"
			//1. scale previous_fs from 80% to 100%
			scale = 0.8 + (1 - now) * 0.2;
			//2. take current_fs to the right(50%) - from 0%
			left = ((1-now) * 50)+"%";
			//3. increase opacity of previous_fs to 1 as it moves in
			opacity = 1 - now;
			current_fs.css({'left': left});
			previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
			$("#msform").css("height", $(this).closest("#msform").find("fieldset:visible").height());
		}, 
		duration: 600, 
		complete: function(){
			current_fs.hide();
			animating = false;
			$("#msform").css("height", $(this).closest("#msform").find("fieldset:visible").height());
			//alert();
		}, 
		//this comes from the custom easing plugin
		easing: 'easeInOutBack'
	});
});

$(".submit").click(function(){
	return false;
})


/* ========================================== 
scrollTop() >= 200
Should be equal the the height of the header
========================================== */

$(window).scroll(function(){
    if ($(window).scrollTop() >= 200) {
        $('header').addClass('fixed-header');
    }
    else {
        $('header').removeClass('fixed-header');
    }
});


// //fILE uPLOAD
// var triggerUpload = document.getElementById('triggerUpload'),
//     upInput = document.getElementById('filePicker'),
//     preview = document.querySelector('.preview');

// //force triggering the file upload here...

// triggerUpload.onclick = function() {
//   upInput.click();
// };


// upInput.onchange = function(e) {

//   var uploaded = this.value,
//       ext = uploaded.substring(uploaded.lastIndexOf('.') + 1),
//       ext = ext.toLowerCase(),
//       fileName = uploaded.substring(uploaded.lastIndexOf("\\") + 1),
//       accepted = ["jpg", "png", "gif", "jpeg"];
  
//   /*
//     ::Add in blank img tag and spinner
//     ::Use FileReader to read the img data
//     ::Set the image source to the FileReader data
//   */
//   function showPreview() {
//       preview.innerHTML = "<div class='loadingLogo'></div>";
// 	    preview.innerHTML += '<img id="img-preview" />';
// 	    var reader = new FileReader();
// 	    reader.onload = function () {
// 	        var img = document.getElementById('img-preview');
// 	        img.src = reader.result;
// 	    };
// 	    reader.readAsDataURL(e.target.files[0]);
//       preview.removeChild(document.querySelector('.loadingLogo'));
//       document.querySelector('.fileName').innerHTML = fileName + "<b> Uploaded!</b>";
//   };
  
//   //only do if supported image file
//   if (new RegExp(accepted.join("|")).test(ext)) {
//     showPreview();
//   } else {
//     preview.innerHTML = "";
//     document.querySelector('.fileName').innerHTML = "Hey! Upload an image file, not a <b>." + ext + "</b> file!";
//   }
  
// }


$(".sidebar-menu li a").filter(function(){
	return this.href == location.href.replace(/#.*/, "");
 }).addClass("active");
 
 $(function() {
	   //var pgurl = window.location.href.substr(window.location.href.lastIndexOf("/") + 1);
	   var pgurl = window.location.href;
  //alert($(this).attr("href"));
	   $(".sidebar-menu li a").each(function() {
	 //alert($(this).attr("href"));
		  if ($(this).attr("href") == pgurl || $(this).attr("href") == '' ){
	   $(this).addClass("active");
	   $(this).parents('.sidebar-dropdown').addClass('active');
	  }
 
		});
	});

//File Uploader (document/ tutor registration page)



//fixed sidebar
//$( '.sidebar' ).fixedsticky();


