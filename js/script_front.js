jQuery(document).ready(function($){
    $('#newsletter_submit').click(function(e) {
        e.preventDefault();
        var mail = $("#mail_newsletter").val();
        var nonce = $("#newsletter_nonce").val();
        var url = $("#newsletter_url").val();

        $verif = checkEmail(mail);
         if ($verif) {
            ajaxNewsletter(mail, nonce, url, $);
         } else {
            alert ("L'e-mail n'est pas valide");
         }
    })

    $('#newsletter_unsubscribe').click(function(e) {
        e.preventDefault();
        var mail = $("#mail").val();
        var nonce = $("#newsletter_unsubscribe_nonce").val();
        var url = $("#newsletter_unsubscribe_url").val();

        $verif = checkEmail(mail);
         if ($verif) {
            ajaxNewsletter_unsubscribe(mail, nonce, url, $);
         } else {
            alert ("L'e-mail n'est pas valide");
         }
    })
})

function checkEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function ajaxNewsletter(mail, nonce, url,$) {
    $.ajax({
        type: "POST",
        url: url,
        data: {
            action : 'newsletter',
            nonce: nonce,
            mail: mail
        },
        success: function(message) {
            $("#message_inscription").html(message);
            $("#mail_newsletter").val('');
        },
        error: function(error) {
            $("#message_desinscription").html('Erreur ajax:'+error);
        }

    });
}

function ajaxNewsletter_unsubscribe(mail, nonce, url, $) {
    $.ajax({
        type: "POST",
        url: url,
        data: {
            action : 'newsletter_unsubscribe',
            nonce: nonce,
            mail: mail
        },
        success: function(message) {
            $("#message_desinscription").html(message);
            $("#mail").val('');
        },
        error: function(error) {
            $("#message_desinscription").html('Erreur ajax:'+error);
        }

    });
    
}