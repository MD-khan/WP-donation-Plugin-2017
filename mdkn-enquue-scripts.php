<?php
function mkdkn_enquue_scripts() { 
    
     wp_enqueue_script('cc-validation', PLUGIN_URL. '/js/creditCardValidator.js');
    
    if( is_page( array( 'donation', 'donation-rohinga', 'donation-omra' ) ) ){
        
         wp_enqueue_script('ajax-donation', PLUGIN_URL. '/js/ajax-donation.js');
         
         wp_localize_script('ajax-donation', 'obj_donation', array(
          'security'     => wp_create_nonce('isbcc'),
          'siteUrl'     => 'https://www.muslimamericansociety.org/donate/process_payment_ajax.php',
          'ChapterId' => 32,
          'Description' => 'Online Donation',
          'FormId'      => 103,
          'ItemId'  => 77,
          'DepartmentId' => 7,
          'InitiativeId' => 13934,
          'AccountId' => 815,
          'FundId' => 6,
          'PaymentTerms' => 1,
          'PaymentMethod' => 5
          
        ));
         
     
       
    }
     
     
      
      
   
}

add_action( 'wp_enqueue_scripts', 'mkdkn_enquue_scripts' ); 