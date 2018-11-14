<?php


function mdkn_donation_form( $atts, $content = null) {
    
     $atts = shortcode_atts(           
                array(
                    'title' => ''
                ), $atts
            );
     
    $form = '<div class="entry-content content">';
    $form = '<h4 id="msg"> '.$atts['title'].'</h4>';
    $form .= '<div class ="form-group">';
    $form .= '<label for="FirstName"> First Name  </label>';
    $form .= ' <input name="FirstName" type="text" id="FirstName">';
    $form .= '<div class ="form-group">';
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="LastName"> Last Name  </label>';
    $form .= ' <input name="LastName" type="text" id="LastName">';
    $form .= '<div class ="form-group">';
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="Email"> Email  </label>';
    $form .= '<input name="Email" type="text" id="Email" size="30" style="width:250px; height:30px;">';
    $form .= '<div class ="form-group">';
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="MobilePhone"> Phone  </label>';
    $form .= '<input name="MobilePhone" type="text" id="MobilePhone" size="30" style="width:250px; height:30px;">';
    $form .= '<div class ="form-group">';
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="AddressLine1"> Address  </label>';
    $form .= '<input name="AddressLine1" type="text" id="AddressLine1" style="width: 250px; height:30px;">';
    $form .= '<div class ="form-group">';
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="City"> City  </label>';
    $form .= '<input name="City" type="text" id="City" style="width: 250px; height:30px;">';
    $form .= '<div class ="form-group">';
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="State"> State  </label>';
    $form .= '<input name="State" type="text" id="State" style="width: 250px; height:30px;">';
    $form .= '<div class ="form-group">';
    
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="Amount"> Amount  </label>';
    $form .= '<input name="Amount" id="Amount" style="width: 150px; height:30px; font-size: 18px;" value="">';
    $form .= '<div class ="form-group">';
    
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="PaymentFrequency"> Donation Type  </label>';
    $form .= '<input type="radio" name="PaymentFrequency" id="PaymentFrequency1" value="0" checked="checked">One Time';
    $form .= '<input type="radio" name="PaymentFrequency" id="PaymentFrequency2" value="1">Monthly';
    $form .= '<div class ="form-group">';
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="CreditCardNumber"> Card Number  </label>';
    $form .= '<input name="CreditCardNumber" id="CreditCardNumber" style="width: 250px; height:30px; font-size: 18px;" autocomplete="off">';
    $form .=' <div id="cc-msg"> </div>';
    $form .= '<div class ="form-group">';
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="CreditCardExpirationMonth"> Expiration Date  </label>';
    $form .= ' <select name="CreditCardExpirationMonth" id="CreditCardExpirationMonth" style="width: 100px; height:30px;">
            <option value="" selected="selected">--Select--</option>
            <option value="1">01-January</option>
            <option value="2">02-February</option>
            <option value="3">03-March</option>
            <option value="4">04-April</option>
            <option value="5">05-May</option>
            <option value="6">06-June</option>
            <option value="7">07-July</option>
            <option value="8">08-August</option>
            <option value="9">09-September</option>
            <option value="10">10-October</option>
            <option value="11">11-November</option>
            <option value="12">12-December</option>
        </select>';
    $form .= '<div class ="form-group">';
    
    
    $form .= '<div class ="form-group">';
    $form .= '<label for="CreditCardNumber"> Expiration Year  </label>';
    $form .= '<select name="CreditCardExpirationYear" id="CreditCardExpirationYear" style="width: 100px; height:30px;">
            <option value="" selected="selected">--Select--</option>
            <option value="2017">2017</option>
            <option value="2018">2018</option>
            <option value="2019">2019</option>
            <option value="2020">2020</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>        
        </select>';
    $form .= '<div class ="form-group">';
    
    
    $form .= '<div class ="form-group">';
    $form .= '<input type="submit" name="submitButton" id="submitButton">';
    $form .= '<div class ="form-group">';
    
    $form .= '</div>';
    
    return $form;
    
            
    
}

add_shortcode('donation_form', 'mdkn_donation_form');