<?php

/* 
 * Create some menues on the admin dashboard
 * 
 */

 
function mdkn_admin_donation_menu() {
     
    global $donation , $add_member_setings, $all_members_setings, $message_settings;
    
    $donation = add_menu_page( __( 'Donation', 'tbc' ),__( 'Donation', 'tbc' ),'manage_options','slug-donation','mdkn_donation_admin_page','dashicons-heart', 2 );
 
}

  
add_action('admin_menu', 'mdkn_admin_donation_menu');



function mdkn_donation_admin_page() {
    
}

function mdkn_donation_setting() {
    
}