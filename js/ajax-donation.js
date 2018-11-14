jQuery(document).ready(function($) {
    
    // Credit card validation
    $('#CreditCardNumber').validateCreditCard(function(result) {
        
            $('#cc-msg').html('Card type: ' + (result.card_type == null ? '-' : result.card_type.name)
                     + '<br>Valid: ' + result.valid
                     + '<br>Length valid: ' + result.length_valid
                     + '<br>Luhn valid: ' + result.luhn_valid);
        });
    

  $( "#submitButton" ).click(function() {
    
      var FirstName = $("#FirstName").val();
      var LastName = $("#LastName").val();
      var Email = $("#Email").val();
      var CreditCardExpirationYear = $("#CreditCardExpirationYear").val();
      var CreditCardNumber = $("#CreditCardNumber").val();
      var CreditCardExpirationMonth = $("#CreditCardExpirationMonth").val();
      var Amount = $("#Amount").val();
      
       
           $.ajax({
                url: obj_donation.siteUrl,
                type: 'POST',
                dataType: 'json',
                cache: false,
                
                data: {
                    action: 'send_data_to',
                    FirstName: FirstName,
                    LastName: LastName,
                    Email: Email,
                    CreditCardExpirationYear : CreditCardExpirationYear,
                    CreditCardNumber : CreditCardNumber,
                    CreditCardExpirationMonth : CreditCardExpirationMonth,
                    Amount : Amount,
                    security: obj_donation.security,
                    Description: obj_donation.Description,
                    FormId: obj_donation.FormId,
                    ItemId: obj_donation.ItemId,
                    DepartmentId: obj_donation.DepartmentId,
                    InitiativeId: obj_donation.InitiativeId,
                    AccountId: obj_donation.AccountId,
                    ChapterId: obj_donation.ChapterId,
                    FundId: obj_donation.FundId,
                    PaymentTerms: obj_donation.PaymentTerms,
                    PaymentMethod: obj_donation.PaymentMethod
                   
                },
                
                success: function( response){
                 
                 $('h4#msg').text("Success");
                 
                console.log(JSON.stringify(response));
                    
                },
                
                error: function( error ) {
                     $('h4#msg').html('<div> '+error + '</div>');
                     console.log(JSON.stringify(error));
                }
            });
   });
 
});