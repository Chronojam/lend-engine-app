if(stripePublicApiKey){var handler=StripeCheckout.configure({key:stripePublicApiKey,locale:"auto",token:function(token){$(".stripe-token").val(token.id);$(".payment-form").submit();waitButton($(".payment-submit"))}})}$(document).ready(function(){$(document).on("click",".payment-submit",function(e){var paymentMethod=$(".payment-method");var paymentAmount=$(".payment-amount");if(paymentAmount.val()>0&&paymentMethod.val()==""){alert("Please choose a payment method, or set the amount paid to zero.");return false}if(paymentAmount<minimumPaymentAmount&&paymentMethod.val()==stripePaymentMethodId){alert("Minimum card payment amount is "+minimumPaymentAmount.toFixed(2));paymentAmount.val(minimumPaymentAmount.toFixed(2));return false}if(paymentMethod.val()==stripePaymentMethodId&&paymentMethod.val()&&!$(".stripe-card-id").val()){handler.open({name:orgName,zipCode:false,currency:currencyIsoCode,allowRememberMe:false,email:$(".contact-email").val(),amount:paymentAmount.val()*100+stripePaymentFee*100});e.preventDefault()}else{$(".payment-form").submit();waitButton($(this))}})});$(window).on("popstate",function(){if(handler!=undefined){handler.close()}});function setCard(cardId){var selectedCard=$("#"+cardId);$(".creditCard").removeClass("active");$(".card-select").html("Use this card");selectedCard.addClass("active");selectedCard.find(".card-select").html("This card will be used.");$(".stripe-card-id").val(cardId);$(".payment-method").val(stripePaymentMethodId);setUpSelectMenus()}function showTakePaymentFields(){$("#payment-fields").show();$(".no-payment-needed").hide();setUpSelectMenus()}