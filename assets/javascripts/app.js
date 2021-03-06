/* app.js */

$( document ).ready( function () {

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "extendedTimeOut": 1000,
        "hideDuration": 1000,
        "hideEasing": "linear",
        "hideMethod": "fadeOut",
        "newestOnTop": true,
        "onclick": null,
        "preventDuplicates": false,
        "progressBar": false,
        "positionClass": "toast-top-full-width",
        "showEasing": "linear",
        "showDuration": 1000,
        "showMethod": "fadeIn",
        "timeOut": 5000
    };

    $( document.forms[ 0 ] ).on( "submit", function ( e ) {
        e.preventDefault();

        var form = document.forms[ 0 ];

        var body = form.querySelector( "textarea[name=\"body\"]" ),
            from_name = form.querySelector( "input[name=\"from_name\"]" ),
            from_address = form.querySelector( "input[type=\"email\"]" ),
            subject = form.querySelector( "input[name=\"subject\"]" );

        var data = [
            {
                "name": "body",
                "value": body.value,
            },
            {
                "name": "from_name",
                "value": from_name.value,
            },
            {
                "name": "from_address",
                "value": from_address.value,
            },
            {
                "name": "subject",
                "value": subject.value,
            }
        ];

        var action = form.getAttribute( "action" );

        $( "button#form-submit-button" ).html(
            "<i class=\"fa fa-fw fa-pulse fa-spinner\"></i>"
        ).attr( "disabled", "disabled" );

        $.ajax( {
            url: action,
            data: data,
            dataType: "json",
            type: "POST"
        } ).done( function ( response ) {
            $( "button#form-submit-button" ).html(
                "<i class=\"fa fa-fw fa-send\"></i>"
            ).removeAttr( "disabled" );

            if ( response.success ) {
                toastr[ "success" ]( l10n.messages.success );
            } else {
                toastr[ "error" ]( l10n.messages.error );
            }
        } );

        return false;
    } );

} );
