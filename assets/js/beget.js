jQuery(document).ready(function($) {    
    if ($('#submit').length > 0) {
        beget_enable_button();
    }

    $('#login, #pass').keyup(function(){        
        beget_enable_button();
    });

    $('#check').on('click', function(){       
        var login = $('#login').val();
        var pass = $('#pass').val();

        if ( login == '' || pass == '') {
            $( '#beget_message' ).html( return_beget_message( 'error', beget_loco.empty_fields ));
            window.beget_api_check = false;
        } else {
            var data = {
                'action' : 'check_api',
                'login' : login,
                'pass' : pass
            };
    
            jQuery.post( ajaxurl, data, function(response) {
                var parsed = JSON.parse(response);            
                $( '#beget_message' ).html( return_beget_message( parsed.status, parsed.message ) );            
                beget_enable_button();
            });
        }
    });
});

function return_beget_message(type, message) {
    var divclass = '';
    if (type == 'success') {
        divclass = 'updated notice';
        window.beget_api_check = true;
    } else {
        divclass = 'error notice-error';
        window.beget_api_check = false;
    }

    var html = ['<div class="' + divclass + '">', 
    '   <p><strong>' + message + '</strong></p>',    
    '   <span class="screen-reader-text">' + message + '</span></button>',
    '</div>'].join('');

    return html;
}

function beget_enable_button() {
    $ = jQuery;    
    if( $('#login').val() != '' && $('#pass').val() != '' && window.beget_api_check ) {
        document.getElementById('submit').disabled = false;
        $('.beget_attention').hide();
    } else {
        document.getElementById('submit').disabled = true; 
    }
}