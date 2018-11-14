<?php

function mdkn_send_data_to() {
    
     /*  
    if (!check_ajax_referer('wp-job-order', 'security')) {
        
        return wp_send_json_error('Invalid Nonce');
    }
        
    if( !current_user_can('manage_options') ) {
         return wp_send_json_error('You are not allow to do this');
    }
      * 
      * 
   
    
    $FirstName = $_POST['FirstName'];
    $LastName = $_POST['LastName'];
    $Email = $_POST['Email'];
    $MobilePhone = $_POST['MobilePhone'];
    $AddressLine1 = $_POST['AddressLine1'];
    $City = $_POST['City'];
    $State = $_POST['State'];
    $Amount = $_POST['Amount'];
    $PaymentFrequency = $_POST['PaymentFrequency'];
    $CreditCardNumber = $_POST['CreditCardNumber'];
    $CreditCardExpirationMonth = $_POST['CreditCardExpirationMonth'];
    $CreditCardExpirationYear = $_POST['CreditCardExpirationYear'];
    
   
      
     wp_send_json_success('Post Saved');
        */
    
    $FirstName = "MD";
    $LastName = "KHAN";
    $Email = "md.monir.khan707@gmial.com";
    $MobilePhone = "617-866-3824";
    $AddressLine1 = "192 London St";
    $City = "Boston";
    $State = "MA";
    $Amount = 5;
    $PaymentFrequency = 0;
    $CreditCardNumber = 5424181325944722;
    $CreditCardExpirationMonth = 3;
    $CreditCardExpirationYear = 2020;
    $ChapterId = 32;
    
  
    
}

add_action( 'wp_ajax_nopriv_send_data_to', 'mdkn_send_data_to' );

add_action( 'wp_ajax_send_data_to', 'mdkn_send_data_to' );

