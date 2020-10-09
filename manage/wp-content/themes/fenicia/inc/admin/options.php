<?php

add_action('init','of_options');

if (!function_exists('of_options')) {
	function of_options() {
		// Access the WordPress Pages via an Array
		$of_pages = array();
		$of_pages_obj = get_pages( 'parent=0' );
		foreach ( $of_pages_obj as $of_page ) {
			$of_pages[$of_page->post_name] = $of_page->post_title;
		}
		$of_pages_tmp = array_unshift( $of_pages, __( 'Select a page:', 'autos' ) );
		
		// get layout images
		$admin_img_path =  ADMIN_DIR . 'assets/images/';
		$portfolio_layouts = array(
			'3' => $admin_img_path . '4-col-portfolio.png',
			'4' => $admin_img_path . '3-col-portfolio.png',
			'6' => $admin_img_path . '2-col-portfolio.png',
		);
		$portfolio_layouts_keys = array_keys($portfolio_layouts);
		$sidebar_layouts = array(
			'right' => $admin_img_path . '2cr.png',
			'left' => $admin_img_path . '2cl.png',
		);
		$sidebar_layouts_keys = array_keys($sidebar_layouts);
		
		// Google Fonts
		$google_fonts = _google_fonts();
		
		// Set the Options Array
		global $of_options;
		$of_options = array();
		
		/* ========== General Settings of Theme ========== */
		$of_options[] = array(
			"name" => __('General Settings','autos'),
			"class" => "general",
			"type" => "heading"
		);
		
		/* ========== Options Of General Settings ========== */
		/* ===== Main Logo ===== */
		
		$of_options[] = array(
			"name" => __('Main Logo', 'autos'),
			"desc" => __('Select a graphic logo to be used instead of the text version - it will get resized to fit the theme\'s design. <span style="color:#d52">Recommended size (to fit to screen): 273px x 112px</span>', 'autos'),
			"id" => "custom_logo",
			"std" => '',
			"type" => "media"
		);
		
		$of_options[] = array(
			"name" => __('Footer Logo', 'autos'),
			"desc" => __('Select a graphic logo to be used instead of the text version - it will get resized to fit the theme\'s design. <span style="color:#d52">Recommended size (to fit to screen): 273px x 112px</span>', 'autos'),
			"id" => "footer_logo",
			"std" => '',
			"type" => "media"
		);
		
		/* ===== Custom Favicon ===== */
		$of_options[] = array(
			"name" => __('Custom Favicon', 'autos'),
			"desc" => __("Upload a icon format (.ico) image that will represent your website's favicon. <span style='color:#d52'>Recommended size (to fit to screen): 16px x 16px </span>", 'autos'),
			"id" => "custom_favicon",
			"std" => '',
			"type" => "media"
		);
		

		/* ===== Footer Logo ===== */
		$of_options[] = array(
			"name" => __('Footer Text', 'autos'),
			"desc" => __("Footer Text for Home and Inner page ", 'autos'),
			"id" => "footer_text",
			 
			"type" => "textarea"
		);
		 
		
		/* ===== Custom CSS ===== */
		$of_options[] = array(
			"name" => __('Custom CSS', 'autos'),
			"desc" => __('Quickly add some CSS to your theme by adding it into this block - it will be inserted after the theme style, so it can override any element. Note: You dont need to write style tag.', 'autos'),
			"id" => "custom_css",
			"options" => array("rows" => 12),
			"std" => "",
			"type" => "textarea"
		);
		
		/* ========== End Of General Settings ========== */
		
		/* ========== Home Page Settings ========== */
		$of_options[] = array(
			"name" => __('Home Page', 'autos'),
			"type" => "heading"
		);
		
		/* ===== Home Page Booking Section Catchphrase ===== */
		$of_options[] = array(
			"name" => __('Home Page Middle Section Heading', 'autos'),
			"desc" => __("Upload A small text for home page middle section. <span style='color:#d52'>You can use the strong tag to highlight catchphrases</span>", 'autos'),
			"id" => "home_page_middle_section_text",
			"std" => '',
			"type" => "textarea"
		);
		
		
		
		/* ========== End Of Home Page Settings ========== */
		
		/* ========== Contact Info Settings ========== */
		$of_options[] = array(
			"name" => __('Contact Info', 'autos'),
			"type" => "heading"
		);
		
		/* ===== ADDING BASIC INFORMATIONS ===== */
		
		$of_options[] = array(
			"id" => "contact_basic_info",
			"std" => 'This section will display the basic info, you need for website eg. phone no, email id, etc...',
			"type" => "info"
		);
		
		/* == INCLUDING GOOGLE MAP == */
		$of_options[] = array(
			"name" => __('Contact GMap Address', 'autos'),
			"desc" => __('Introduce a Google Maps Address code that gets showed on the contact page.', 'autos'),
			"id" => "contact_gmap",
			"std" => '',
			"type" => "text"
		);
         
         $of_options[] = array(
			"name" => __('Contact Header GMap Address', 'autos'),
			"desc" => __('Introduce a Google Maps Address code that gets showed on the contact page.', 'autos'),
			"id" => "header_contact_gmap",
			"std" => '',
			"type" => "text"
		);
        $of_options[] = array(
			"name" => __('Operational Hours', 'autos'),
			 
			"id" => "op_hours",
			"std" => '',
			"type" => "text"
		); 
		/* == INCLUDING PHONE NUMBER == */
		$of_options[] = array(
			"name" => __('Contact Phone No', 'autos'),
			"desc" => __('Put the phone number you want to display on the website, which the user will be able to contact.', 'autos'),
			"id" => "contact_phone_no",
			"std" => '',
			"type" => "text"
		);
        
		$of_options[] = array(
			"name" => __('Contact Landline No', 'autos'),
			"desc" => __('Put the phone number you want to display on the website, which the user will be able to contact.', 'autos'),
			"id" => "contact_landline_no",
			"std" => '',
			"type" => "text"
		);
        $of_options[] = array(
			"name" => __('Contact Mobile No', 'autos'),
			"desc" => __('Put the phone number you want to display on the website, which the user will be able to contact.', 'autos'),
			"id" => "contact_mobile_no",
			"std" => '',
			"type" => "text"
		);
		$of_options[] = array(
			"name" => __('Contact Fax No', 'autos'),
			"desc" => __('Put the Fax number you want to display on the website, which the user will be able to contact.', 'autos'),
			"id" => "contact_fax_no",
			"std" => '',
			"type" => "text"
		);
		/* == INCLUDING EMAIL ID ==*/
		$of_options[] = array(
			"name" => __('Contact Email ID', 'autos'),
			"desc" => __('Put the Email ID you want to display on the website , which the user will be able to contact.', 'autos'),
			"id" => "contact_email_id",
			"std" => '',
			"type" => "text"
		);
		

		/* == INCLUDING ADDRESS ==*/
		$of_options[] = array(
			"name" => __('Post Box Address', 'autos'),
			"desc" => __('Put the Address you want to display on the website.', 'autos'),
			"id" => "post_box_address",
			"std" => '',
			"type" => "text"
		);

		/* ===== END OF ADDING BASIC INFORMATIONS ===== */
		
		/* ===== ADDING SOCIAL INFORMATIONS ===== */
		
		$of_options[] = array(
			"id" => "contact_social_info",
			"std" => 'This section will display the social info, you need for website eg. facebook, youtube, google plus etc...',
			"type" => "info"
		);
		
		/* == INCLUDING FACEBOOK LINK ==*/
		$of_options[] = array(
			"name" => __('Facebook Page Link', 'autos'),
			"desc" => __('This will open up a new tab along with the facebook link of your project (For more details See <a href="https://facebook.com/" target="_blank">Facebook</a>).', 'autos'),
			"id" => "facebook_link",
			"std" => '',
			"type" => "text"
		);
		
		/* == INCLUDING TWITTER LINK ==*/
		$of_options[] = array(
			"name" => __('Twitter Page Link', 'autos'),
			"desc" => __('This will open up a new tab along with the twitter link of your project (For more details See <a href="https://twitter.com/" target="_blank">Twitter</a>).', 'autos'),
			"id" => "twitter_link",
			"std" => '',
			"type" => "text"
		);
		
		/* == INCLUDING GOOGLE PLUS LINK ==*/
		$of_options[] = array(
			"name" => __('Google Plus Page Link', 'autos'),
			"desc" => __('This will open up a new tab along with the google plus link of your project (For more details See <a href="https://plus.google.com/" target="_blank">Google Plus</a>).', 'autos'),
			"id" => "gplus_link",
			"std" => '',
			"type" => "text"
		);
		
		/* == INCLUDING INSTAGRAM LINK ==*/
		$of_options[] = array(
			"name" => __('Instagram Link', 'autos'),
			"desc" => __('This will open up a new tab along with the instagram link of your project (For more details See <a href="https://instagram.com/" target="_blank">Youtube</a>).', 'autos'),
			"id" => "instagram_link",
			"std" => '',
			"type" => "text"
		);
		/* == INCLUDING INSTAGRAM LINK ==*/
		$of_options[] = array(
			"name" => __('Pinterest Link', 'autos'),
			"desc" => __('This will open up a new tab along with the Pinterest link of your project (For more details See <a href="https://instagram.com/" target="_blank">Youtube</a>).', 'autos'),
			"id" => "pinterest_link",
			"std" => '',
			"type" => "text"
		);		

		/* == INCLUDING LINKEDIN LINK ==*/
		$of_options[] = array(
			"name" => __('Linkedin Link', 'autos'),
			"desc" => __('This will open up a new tab along with the linkedin link of your project (For more details See <a href="https://linkedin.com/" target="_blank">Linkedin</a>).', 'autos'),
			"id" => "linkedin_link",
			"std" => '',
			"type" => "text"
		);
	
		/* == INCLUDING YOUTUBE LINK ==*/
		$of_options[] = array(
			"name" => __('Youtube Channel Link', 'autos'),
			"desc" => __('This will open up a new tab along with the linkedin link of your project (For more details See <a href="https://youtube.com/" target="_blank">Youtube</a>).', 'autos'),
			"id" => "youtube_link",
			"std" => '',
			"type" => "text"
		);
		/* ===== END OF CONTACT SETTINGS ===== */
			
		// Other Settings
		$of_options[] = array(
			"name" => __('Other Settings', 'autos'),
			"class" => "other",
			"type" => "heading"
		);
				
		$of_options[] = array(
			"name" => __('Taxonomy Banner Image', 'autos'),
			"desc" => __('Select an image to be used as a Taxonomy Banner. (Recommended size: <span style="color:#d52">1349 x 423</span> for best fit.)', 'autos'),
			"id" => "tax_banner_image",
			"std" => '',
			"type" => "media"
		);
		$of_options[] = array(
			"name" => __('Single Banner Image', 'autos'),
			"desc" => __('Select an image to be used as a Single Banner Image. (Recommended size: <span style="color:#d52">1349 x 423</span> for best fit.)', 'autos'),
			"id" => "single_banner_image",
			"std" => '',
			"type" => "media"
		);
		$of_options[] = array(
			"name" => __('Not found Image', 'autos'),
			"desc" => __('Select an image to be used as a Not found Image.', 'autos'),
			"id" => "_not_found",
			"std" => '',
			"type" => "media"
		);
		$of_options[] = array(
			"name" => __('404 Image', 'autos'),
			"desc" => __('Select an image to be used as a Not found Image.', 'autos'),
			"id" => "404_image",
			"std" => '',
			"type" => "media"
		);
 		$of_options[] = array(
			"name" => __('404 Banner Image', 'autos'),
			"desc" => __('Select an image to be used as a 404 page not found graphics. (Recommended size: <span style="color:#d52">1349 x 423</span> for best fit.)', 'autos'),
			"id" => "404_banner_image",
			"std" => '',
			"type" => "media"
		);
		
		$of_options[] = array(
			"name" => __('404 Heading', 'autos'),
			"desc" => __('Give heading for 404 page.', 'autos'),
			"id" => "404_text_heading",
			"std" => '',
			"type" => "text"
		);
		
		$of_options[] = array(
			"name" => __('404 Text', 'autos'),
			"desc" => __('Input your error message that gets displayed on the 404 page not found template.', 'autos'),
			"id" => "404_text",
			"std" => 'The page you are looking for has vanished. Maybe it was never here or it was moved to a better place. You\'ll never know.',
			"type" => "textarea"
		);
	/* ========== END OF 404 PAGE ========== */
		
		// Backup Options
		$of_options[] = array(
			"name" => __('Backup Options', 'autos'),
			"class" => "backup",
			"type" => "heading"
		);
		
		$of_options[] = array(
			"name" => __('Backup and Restore Options', 'autos'),
			"id" => "of_backup",
			"std" => "",
			"type" => "backup",
			"desc" => __('You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back.', ''),
		);
		
		$of_options[] = array(
			"name" => __('Transfer Theme Options Data', 'autos'),
			"id" => "of_transfer",
			"std" => "",
			"type" => "transfer",
			"desc" => __('You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click "Import Options".', 'autos'),
		);
		
		}
		
		function _google_fonts() {
			return array(""=>"","ABeeZee"=>"ABeeZee","Abel"=>"Abel","Abril Fatface"=>"Abril Fatface","Aclonica"=>"Aclonica","Acme"=>"Acme","Actor"=>"Actor","Adamina"=>"Adamina","Advent Pro"=>"Advent Pro","Aguafina Script"=>"Aguafina Script","Akronim"=>"Akronim","Aladin"=>"Aladin","Aldrich"=>"Aldrich","Alef"=>"Alef","Alegreya"=>"Alegreya","Alegreya SC"=>"Alegreya SC","Alex Brush"=>"Alex Brush","Alfa Slab One"=>"Alfa Slab One","Alice"=>"Alice","Alike"=>"Alike","Alike Angular"=>"Alike Angular","Allan"=>"Allan","Allerta"=>"Allerta","Allerta Stencil"=>"Allerta Stencil","Allura"=>"Allura","Almendra"=>"Almendra","Almendra Display"=>"Almendra Display","Almendra SC"=>"Almendra SC","Amarante"=>"Amarante","Amaranth"=>"Amaranth","Amatic SC"=>"Amatic SC","Amethysta"=>"Amethysta","Anaheim"=>"Anaheim","Andada"=>"Andada","Andika"=>"Andika","Angkor"=>"Angkor","Annie Use Your Telescope"=>"Annie Use Your Telescope","Anonymous Pro"=>"Anonymous Pro","Antic"=>"Antic","Antic Didone"=>"Antic Didone","Antic Slab"=>"Antic Slab","Anton"=>"Anton","Arapey"=>"Arapey","Arbutus"=>"Arbutus","Arbutus Slab"=>"Arbutus Slab","Architects Daughter"=>"Architects Daughter","Archivo Black"=>"Archivo Black","Archivo Narrow"=>"Archivo Narrow","Arimo"=>"Arimo","Arizonia"=>"Arizonia","Armata"=>"Armata","Artifika"=>"Artifika","Arvo"=>"Arvo","Asap"=>"Asap","Asset"=>"Asset","Astloch"=>"Astloch","Asul"=>"Asul","Atomic Age"=>"Atomic Age","Aubrey"=>"Aubrey","Audiowide"=>"Audiowide","Autour One"=>"Autour One","Average"=>"Average","Average Sans"=>"Average Sans","Averia Gruesa Libre"=>"Averia Gruesa Libre","Averia Libre"=>"Averia Libre","Averia Sans Libre"=>"Averia Sans Libre","Averia Serif Libre"=>"Averia Serif Libre","Bad Script"=>"Bad Script","Balthazar"=>"Balthazar","Bangers"=>"Bangers","Basic"=>"Basic","Battambang"=>"Battambang","Baumans"=>"Baumans","Bayon"=>"Bayon","Belgrano"=>"Belgrano","Belleza"=>"Belleza","BenchNine"=>"BenchNine","Bentham"=>"Bentham","Berkshire Swash"=>"Berkshire Swash","Bevan"=>"Bevan","Bigelow Rules"=>"Bigelow Rules","Bigshot One"=>"Bigshot One","Bilbo"=>"Bilbo","Bilbo Swash Caps"=>"Bilbo Swash Caps","Bitter"=>"Bitter","Black Ops One"=>"Black Ops One","Bokor"=>"Bokor","Bonbon"=>"Bonbon","Boogaloo"=>"Boogaloo","Bowlby One"=>"Bowlby One","Bowlby One SC"=>"Bowlby One SC","Brawler"=>"Brawler","Bree Serif"=>"Bree Serif","Bubblegum Sans"=>"Bubblegum Sans","Bubbler One"=>"Bubbler One","Buda"=>"Buda","Buenard"=>"Buenard","Butcherman"=>"Butcherman","Butterfly Kids"=>"Butterfly Kids","Cabin"=>"Cabin","Cabin Condensed"=>"Cabin Condensed","Cabin Sketch"=>"Cabin Sketch","Caesar Dressing"=>"Caesar Dressing","Cagliostro"=>"Cagliostro","Calligraffitti"=>"Calligraffitti","Cambo"=>"Cambo","Candal"=>"Candal","Cantarell"=>"Cantarell","Cantata One"=>"Cantata One","Cantora One"=>"Cantora One","Capriola"=>"Capriola","Cardo"=>"Cardo","Carme"=>"Carme","Carrois Gothic"=>"Carrois Gothic","Carrois Gothic SC"=>"Carrois Gothic SC","Carter One"=>"Carter One","Caudex"=>"Caudex","Cedarville Cursive"=>"Cedarville Cursive","Ceviche One"=>"Ceviche One","Changa One"=>"Changa One","Chango"=>"Chango","Chau Philomene One"=>"Chau Philomene One","Chela One"=>"Chela One","Chelsea Market"=>"Chelsea Market","Chenla"=>"Chenla","Cherry Cream Soda"=>"Cherry Cream Soda","Cherry Swash"=>"Cherry Swash","Chewy"=>"Chewy","Chicle"=>"Chicle","Chivo"=>"Chivo","Cinzel"=>"Cinzel","Cinzel Decorative"=>"Cinzel Decorative","Clicker Script"=>"Clicker Script","Coda"=>"Coda","Coda Caption"=>"Coda Caption","Codystar"=>"Codystar","Combo"=>"Combo","Comfortaa"=>"Comfortaa","Coming Soon"=>"Coming Soon","Concert One"=>"Concert One","Condiment"=>"Condiment","Content"=>"Content","Contrail One"=>"Contrail One","Convergence"=>"Convergence","Cookie"=>"Cookie","Copse"=>"Copse","Corben"=>"Corben","Courgette"=>"Courgette","Cousine"=>"Cousine","Coustard"=>"Coustard","Covered By Your Grace"=>"Covered By Your Grace","Crafty Girls"=>"Crafty Girls","Creepster"=>"Creepster","Crete Round"=>"Crete Round","Crimson Text"=>"Crimson Text","Croissant One"=>"Croissant One","Crushed"=>"Crushed","Cuprum"=>"Cuprum","Cutive"=>"Cutive","Cutive Mono"=>"Cutive Mono","Damion"=>"Damion","Dancing Script"=>"Dancing Script","Dangrek"=>"Dangrek","Dawning of a New Day"=>"Dawning of a New Day","Days One"=>"Days One","Delius"=>"Delius","Delius Swash Caps"=>"Delius Swash Caps","Delius Unicase"=>"Delius Unicase","Della Respira"=>"Della Respira","Denk One"=>"Denk One","Devonshire"=>"Devonshire","Didact Gothic"=>"Didact Gothic","Diplomata"=>"Diplomata","Diplomata SC"=>"Diplomata SC","Domine"=>"Domine","Donegal One"=>"Donegal One","Doppio One"=>"Doppio One","Dorsa"=>"Dorsa","Dosis"=>"Dosis","Dr Sugiyama"=>"Dr Sugiyama","Droid Sans"=>"Droid Sans","Droid Sans Mono"=>"Droid Sans Mono","Droid Serif"=>"Droid Serif","Duru Sans"=>"Duru Sans","Dynalight"=>"Dynalight","EB Garamond"=>"EB Garamond","Eagle Lake"=>"Eagle Lake","Eater"=>"Eater","Economica"=>"Economica","Electrolize"=>"Electrolize","Elsie"=>"Elsie","Elsie Swash Caps"=>"Elsie Swash Caps","Emblema One"=>"Emblema One","Emilys Candy"=>"Emilys Candy","Engagement"=>"Engagement","Englebert"=>"Englebert","Enriqueta"=>"Enriqueta","Erica One"=>"Erica One","Esteban"=>"Esteban","Euphoria Script"=>"Euphoria Script","Ewert"=>"Ewert","Exo"=>"Exo","Expletus Sans"=>"Expletus Sans","Fanwood Text"=>"Fanwood Text","Fascinate"=>"Fascinate","Fascinate Inline"=>"Fascinate Inline","Faster One"=>"Faster One","Fasthand"=>"Fasthand","Fauna One"=>"Fauna One","Federant"=>"Federant","Federo"=>"Federo","Felipa"=>"Felipa","Fenix"=>"Fenix","Finger Paint"=>"Finger Paint","Fjalla One"=>"Fjalla One","Fjord One"=>"Fjord One","Flamenco"=>"Flamenco","Flavors"=>"Flavors","Fondamento"=>"Fondamento","Fontdiner Swanky"=>"Fontdiner Swanky","Forum"=>"Forum","Francois One"=>"Francois One","Freckle Face"=>"Freckle Face","Fredericka the Great"=>"Fredericka the Great","Fredoka One"=>"Fredoka One","Freehand"=>"Freehand","Fresca"=>"Fresca","Frijole"=>"Frijole","Fruktur"=>"Fruktur","Fugaz One"=>"Fugaz One","GFS Didot"=>"GFS Didot","GFS Neohellenic"=>"GFS Neohellenic","Gabriela"=>"Gabriela","Gafata"=>"Gafata","Galdeano"=>"Galdeano","Galindo"=>"Galindo","Gentium Basic"=>"Gentium Basic","Gentium Book Basic"=>"Gentium Book Basic","Geo"=>"Geo","Geostar"=>"Geostar","Geostar Fill"=>"Geostar Fill","Germania One"=>"Germania One","Gilda Display"=>"Gilda Display","Give You Glory"=>"Give You Glory","Glass Antiqua"=>"Glass Antiqua","Glegoo"=>"Glegoo","Gloria Hallelujah"=>"Gloria Hallelujah","Goblin One"=>"Goblin One","Gochi Hand"=>"Gochi Hand","Gorditas"=>"Gorditas","Goudy Bookletter 1911"=>"Goudy Bookletter 1911","Graduate"=>"Graduate","Grand Hotel"=>"Grand Hotel","Gravitas One"=>"Gravitas One","Great Vibes"=>"Great Vibes","Griffy"=>"Griffy","Gruppo"=>"Gruppo","Gudea"=>"Gudea","Habibi"=>"Habibi","Hammersmith One"=>"Hammersmith One","Hanalei"=>"Hanalei","Hanalei Fill"=>"Hanalei Fill","Handlee"=>"Handlee","Hanuman"=>"Hanuman","Happy Monkey"=>"Happy Monkey","Headland One"=>"Headland One","Henny Penny"=>"Henny Penny","Herr Von Muellerhoff"=>"Herr Von Muellerhoff","Holtwood One SC"=>"Holtwood One SC","Homemade Apple"=>"Homemade Apple","Homenaje"=>"Homenaje","IM Fell DW Pica"=>"IM Fell DW Pica","IM Fell DW Pica SC"=>"IM Fell DW Pica SC","IM Fell Double Pica"=>"IM Fell Double Pica","IM Fell Double Pica SC"=>"IM Fell Double Pica SC","IM Fell English"=>"IM Fell English","IM Fell English SC"=>"IM Fell English SC","IM Fell French Canon"=>"IM Fell French Canon","IM Fell French Canon SC"=>"IM Fell French Canon SC","IM Fell Great Primer"=>"IM Fell Great Primer","IM Fell Great Primer SC"=>"IM Fell Great Primer SC","Iceberg"=>"Iceberg","Iceland"=>"Iceland","Imprima"=>"Imprima","Inconsolata"=>"Inconsolata","Inder"=>"Inder","Indie Flower"=>"Indie Flower","Inika"=>"Inika","Irish Grover"=>"Irish Grover","Istok Web"=>"Istok Web","Italiana"=>"Italiana","Italianno"=>"Italianno","Jacques Francois"=>"Jacques Francois","Jacques Francois Shadow"=>"Jacques Francois Shadow","Jim Nightshade"=>"Jim Nightshade","Jockey One"=>"Jockey One","Jolly Lodger"=>"Jolly Lodger","Josefin Sans"=>"Josefin Sans","Josefin Slab"=>"Josefin Slab","Joti One"=>"Joti One","Judson"=>"Judson","Julee"=>"Julee","Julius Sans One"=>"Julius Sans One","Junge"=>"Junge","Jura"=>"Jura","Just Another Hand"=>"Just Another Hand","Just Me Again Down Here"=>"Just Me Again Down Here","Kameron"=>"Kameron","Karla"=>"Karla","Kaushan Script"=>"Kaushan Script","Kavoon"=>"Kavoon","Keania One"=>"Keania One","Kelly Slab"=>"Kelly Slab","Kenia"=>"Kenia","Khmer"=>"Khmer","Kite One"=>"Kite One","Knewave"=>"Knewave","Kotta One"=>"Kotta One","Koulen"=>"Koulen","Kranky"=>"Kranky","Kreon"=>"Kreon","Kristi"=>"Kristi","Krona One"=>"Krona One","La Belle Aurore"=>"La Belle Aurore","Lancelot"=>"Lancelot","Lato"=>"Lato","League Script"=>"League Script","Leckerli One"=>"Leckerli One","Ledger"=>"Ledger","Lekton"=>"Lekton","Lemon"=>"Lemon","Libre Baskerville"=>"Libre Baskerville","Life Savers"=>"Life Savers","Lilita One"=>"Lilita One","Lily Script One"=>"Lily Script One","Limelight"=>"Limelight","Linden Hill"=>"Linden Hill","Lobster"=>"Lobster","Lobster Two"=>"Lobster Two","Londrina Outline"=>"Londrina Outline","Londrina Shadow"=>"Londrina Shadow","Londrina Sketch"=>"Londrina Sketch","Londrina Solid"=>"Londrina Solid","Lora"=>"Lora","Love Ya Like A Sister"=>"Love Ya Like A Sister","Loved by the King"=>"Loved by the King","Lovers Quarrel"=>"Lovers Quarrel","Luckiest Guy"=>"Luckiest Guy","Lusitana"=>"Lusitana","Lustria"=>"Lustria","Macondo"=>"Macondo","Macondo Swash Caps"=>"Macondo Swash Caps","Magra"=>"Magra","Maiden Orange"=>"Maiden Orange","Mako"=>"Mako","Marcellus"=>"Marcellus","Marcellus SC"=>"Marcellus SC","Marck Script"=>"Marck Script","Margarine"=>"Margarine","Marko One"=>"Marko One","Marmelad"=>"Marmelad","Marvel"=>"Marvel","Mate"=>"Mate","Mate SC"=>"Mate SC","Maven Pro"=>"Maven Pro","McLaren"=>"McLaren","Meddon"=>"Meddon","MedievalSharp"=>"MedievalSharp","Medula One"=>"Medula One","Megrim"=>"Megrim","Meie Script"=>"Meie Script","Merienda"=>"Merienda","Merienda One"=>"Merienda One","Merriweather"=>"Merriweather","Merriweather Sans"=>"Merriweather Sans","Metal"=>"Metal","Metal Mania"=>"Metal Mania","Metamorphous"=>"Metamorphous","Metrophobic"=>"Metrophobic","Michroma"=>"Michroma","Milonga"=>"Milonga","Miltonian"=>"Miltonian","Miltonian Tattoo"=>"Miltonian Tattoo","Miniver"=>"Miniver","Miss Fajardose"=>"Miss Fajardose","Modern Antiqua"=>"Modern Antiqua","Molengo"=>"Molengo","Molle"=>"Molle","Monda"=>"Monda","Monofett"=>"Monofett","Monoton"=>"Monoton","Monsieur La Doulaise"=>"Monsieur La Doulaise","Montaga"=>"Montaga","Montez"=>"Montez","Montserrat"=>"Montserrat","Montserrat Alternates"=>"Montserrat Alternates","Montserrat Subrayada"=>"Montserrat Subrayada","Moul"=>"Moul","Moulpali"=>"Moulpali","Mountains of Christmas"=>"Mountains of Christmas","Mouse Memoirs"=>"Mouse Memoirs","Mr Bedfort"=>"Mr Bedfort","Mr Dafoe"=>"Mr Dafoe","Mr De Haviland"=>"Mr De Haviland","Mrs Saint Delafield"=>"Mrs Saint Delafield","Mrs Sheppards"=>"Mrs Sheppards","Muli"=>"Muli","Mystery Quest"=>"Mystery Quest","Neucha"=>"Neucha","Neuton"=>"Neuton","New Rocker"=>"New Rocker","News Cycle"=>"News Cycle","Niconne"=>"Niconne","Nixie One"=>"Nixie One","Nobile"=>"Nobile","Nokora"=>"Nokora","Norican"=>"Norican","Nosifer"=>"Nosifer","Nothing You Could Do"=>"Nothing You Could Do","Noticia Text"=>"Noticia Text","Noto Sans"=>"Noto Sans","Noto Serif"=>"Noto Serif","Nova Cut"=>"Nova Cut","Nova Flat"=>"Nova Flat","Nova Mono"=>"Nova Mono","Nova Oval"=>"Nova Oval","Nova Round"=>"Nova Round","Nova Script"=>"Nova Script","Nova Slim"=>"Nova Slim","Nova Square"=>"Nova Square","Numans"=>"Numans","Nunito"=>"Nunito","Odor Mean Chey"=>"Odor Mean Chey","Offside"=>"Offside","Old Standard TT"=>"Old Standard TT","Oldenburg"=>"Oldenburg","Oleo Script"=>"Oleo Script","Oleo Script Swash Caps"=>"Oleo Script Swash Caps","Open Sans"=>"Open Sans","Open Sans Condensed"=>"Open Sans Condensed","Oranienbaum"=>"Oranienbaum","Orbitron"=>"Orbitron","Oregano"=>"Oregano","Orienta"=>"Orienta","Original Surfer"=>"Original Surfer","Oswald"=>"Oswald","Over the Rainbow"=>"Over the Rainbow","Overlock"=>"Overlock","Overlock SC"=>"Overlock SC","Ovo"=>"Ovo","Oxygen"=>"Oxygen","Oxygen Mono"=>"Oxygen Mono","PT Mono"=>"PT Mono","PT Sans"=>"PT Sans","PT Sans Caption"=>"PT Sans Caption","PT Sans Narrow"=>"PT Sans Narrow","PT Serif"=>"PT Serif","PT Serif Caption"=>"PT Serif Caption","Pacifico"=>"Pacifico","Paprika"=>"Paprika","Parisienne"=>"Parisienne","Passero One"=>"Passero One","Passion One"=>"Passion One","Pathway Gothic One"=>"Pathway Gothic One","Patrick Hand"=>"Patrick Hand","Patrick Hand SC"=>"Patrick Hand SC","Patua One"=>"Patua One","Paytone One"=>"Paytone One","Peralta"=>"Peralta","Permanent Marker"=>"Permanent Marker","Petit Formal Script"=>"Petit Formal Script","Petrona"=>"Petrona","Philosopher"=>"Philosopher","Piedra"=>"Piedra","Pinyon Script"=>"Pinyon Script","Pirata One"=>"Pirata One","Plaster"=>"Plaster","Play"=>"Play","Playball"=>"Playball","Playfair Display"=>"Playfair Display","Playfair Display SC"=>"Playfair Display SC","Podkova"=>"Podkova","Poiret One"=>"Poiret One","Poller One"=>"Poller One","Poly"=>"Poly","Pompiere"=>"Pompiere","Pontano Sans"=>"Pontano Sans","Port Lligat Sans"=>"Port Lligat Sans","Port Lligat Slab"=>"Port Lligat Slab","Prata"=>"Prata","Preahvihear"=>"Preahvihear","Press Start 2P"=>"Press Start 2P","Princess Sofia"=>"Princess Sofia","Prociono"=>"Prociono","Prosto One"=>"Prosto One","Puritan"=>"Puritan","Purple Purse"=>"Purple Purse","Quando"=>"Quando","Quantico"=>"Quantico","Quattrocento"=>"Quattrocento","Quattrocento Sans"=>"Quattrocento Sans","Questrial"=>"Questrial","Quicksand"=>"Quicksand","Quintessential"=>"Quintessential","Qwigley"=>"Qwigley","Racing Sans One"=>"Racing Sans One","Radley"=>"Radley","Raleway"=>"Raleway","Raleway Dots"=>"Raleway Dots","Rambla"=>"Rambla","Rammetto One"=>"Rammetto One","Ranchers"=>"Ranchers","Rancho"=>"Rancho","Rationale"=>"Rationale","Redressed"=>"Redressed","Reenie Beanie"=>"Reenie Beanie","Revalia"=>"Revalia","Ribeye"=>"Ribeye","Ribeye Marrow"=>"Ribeye Marrow","Righteous"=>"Righteous","Risque"=>"Risque","Roboto"=>"Roboto","Roboto Condensed"=>"Roboto Condensed","Roboto Slab"=>"Roboto Slab","Rochester"=>"Rochester","Rock Salt"=>"Rock Salt","Rokkitt"=>"Rokkitt","Romanesco"=>"Romanesco","Ropa Sans"=>"Ropa Sans","Rosario"=>"Rosario","Rosarivo"=>"Rosarivo","Rouge Script"=>"Rouge Script","Ruda"=>"Ruda","Rufina"=>"Rufina","Ruge Boogie"=>"Ruge Boogie","Ruluko"=>"Ruluko","Rum Raisin"=>"Rum Raisin","Ruslan Display"=>"Ruslan Display","Russo One"=>"Russo One","Ruthie"=>"Ruthie","Rye"=>"Rye","Sacramento"=>"Sacramento","Sail"=>"Sail","Salsa"=>"Salsa","Sanchez"=>"Sanchez","Sancreek"=>"Sancreek","Sansita One"=>"Sansita One","Sarina"=>"Sarina","Satisfy"=>"Satisfy","Scada"=>"Scada","Schoolbell"=>"Schoolbell","Seaweed Script"=>"Seaweed Script","Sevillana"=>"Sevillana","Seymour One"=>"Seymour One","Shadows Into Light"=>"Shadows Into Light","Shadows Into Light Two"=>"Shadows Into Light Two","Shanti"=>"Shanti","Share"=>"Share","Share Tech"=>"Share Tech","Share Tech Mono"=>"Share Tech Mono","Shojumaru"=>"Shojumaru","Short Stack"=>"Short Stack","Siemreap"=>"Siemreap","Sigmar One"=>"Sigmar One","Signika"=>"Signika","Signika Negative"=>"Signika Negative","Simonetta"=>"Simonetta","Sintony"=>"Sintony","Sirin Stencil"=>"Sirin Stencil","Six Caps"=>"Six Caps","Skranji"=>"Skranji","Slackey"=>"Slackey","Smokum"=>"Smokum","Smythe"=>"Smythe","Sniglet"=>"Sniglet","Snippet"=>"Snippet","Snowburst One"=>"Snowburst One","Sofadi One"=>"Sofadi One","Sofia"=>"Sofia","Sonsie One"=>"Sonsie One","Sorts Mill Goudy"=>"Sorts Mill Goudy","Source Code Pro"=>"Source Code Pro","Source Sans Pro"=>"Source Sans Pro","Special Elite"=>"Special Elite","Spicy Rice"=>"Spicy Rice","Spinnaker"=>"Spinnaker","Spirax"=>"Spirax","Squada One"=>"Squada One","Stalemate"=>"Stalemate","Stalinist One"=>"Stalinist One","Stardos Stencil"=>"Stardos Stencil","Stint Ultra Condensed"=>"Stint Ultra Condensed","Stint Ultra Expanded"=>"Stint Ultra Expanded","Stoke"=>"Stoke","Strait"=>"Strait","Sue Ellen Francisco"=>"Sue Ellen Francisco","Sunshiney"=>"Sunshiney","Supermercado One"=>"Supermercado One","Suwannaphum"=>"Suwannaphum","Swanky and Moo Moo"=>"Swanky and Moo Moo","Syncopate"=>"Syncopate","Tangerine"=>"Tangerine","Taprom"=>"Taprom","Tauri"=>"Tauri","Telex"=>"Telex","Tenor Sans"=>"Tenor Sans","Text Me One"=>"Text Me One","The Girl Next Door"=>"The Girl Next Door","Tienne"=>"Tienne","Tinos"=>"Tinos","Titan One"=>"Titan One","Titillium Web"=>"Titillium Web","Trade Winds"=>"Trade Winds","Trocchi"=>"Trocchi","Trochut"=>"Trochut","Trykker"=>"Trykker","Tulpen One"=>"Tulpen One","Ubuntu"=>"Ubuntu","Ubuntu Condensed"=>"Ubuntu Condensed","Ubuntu Mono"=>"Ubuntu Mono","Ultra"=>"Ultra","Uncial Antiqua"=>"Uncial Antiqua","Underdog"=>"Underdog","Unica One"=>"Unica One","UnifrakturCook"=>"UnifrakturCook","UnifrakturMaguntia"=>"UnifrakturMaguntia","Unkempt"=>"Unkempt","Unlock"=>"Unlock","Unna"=>"Unna","VT323"=>"VT323","Vampiro One"=>"Vampiro One","Varela"=>"Varela","Varela Round"=>"Varela Round","Vast Shadow"=>"Vast Shadow","Vibur"=>"Vibur","Vidaloka"=>"Vidaloka","Viga"=>"Viga","Voces"=>"Voces","Volkhov"=>"Volkhov","Vollkorn"=>"Vollkorn","Voltaire"=>"Voltaire","Waiting for the Sunrise"=>"Waiting for the Sunrise","Wallpoet"=>"Wallpoet","Walter Turncoat"=>"Walter Turncoat","Warnes"=>"Warnes","Wellfleet"=>"Wellfleet","Wendy One"=>"Wendy One","Wire One"=>"Wire One","Yanone Kaffeesatz"=>"Yanone Kaffeesatz","Yellowtail"=>"Yellowtail","Yeseva One"=>"Yeseva One","Yesteryear"=>"Yesteryear","Zeyada"=>"Zeyada");
	}

}