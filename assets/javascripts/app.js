/* app.js */

/* --- */
var app = {};

/**
 * Initialize the app.
 *
 * @param mixed callback
 *
 * @return void
 */
app.init = function ( callback ) {
    if ( document.readyState != "loading" ) {
        callback();
    } else if ( document.addEventListener ) {
        document.addEventListener( "DOMContentLoaded", callback );
    } else {
        document.attachEvent( "onreadystatechange", function () {
            if ( document.readyState != "loading" ) {
                callback();
            }
        } );
    }
}

/**
 * Register event listener to element.
 *
 * @param mixed  element
 * @param string name
 * @param mixed  callback
 *
 * @return void
 */
app.on = function ( element, name, callback ) {
    if ( element.addEventListener ) {
        element.addEventListener( name, callback );
    } else {
        element.attachEvent("on" + name, function () {
            callback.call( element );
        } )
    }
}

/**
 * Send POST data to given URL.
 *
 * @param string   url
 * @param array    data
 * @param callable callback
 *
 * @return void
 */
app.ajax = function ( url, data, callback ) {
    var request = new XMLHttpRequest();

    request.open( "POST", url, true );

    request.setRequestHeader(
        "Content-Type",
        "application/x-www-form-urlencoded; charset=UTF-8"
    );

    request.setRequestHeader(
        "X-Requested-With",
        "XMLHttpRequest"
    );

    request.onreadystatechange = function () {
        if ( this.readyState === 4 ) {
            if ( this.status >= 200 && this.status < 400 ) {
                callback( this.responseText );
            }
        }
    };

    var queryString = "";

    for ( var i = 0; i < data.length; i++ ) {
        queryString += encodeURIComponent( data[ i ].name );
        queryString += "=";
        queryString += encodeURIComponent( data[ i ].value );
        queryString += "&";
    }

    queryString = queryString.substring( 0, queryString.length - 1 );

    request.send( queryString );

    request = null;
}

/* --- */

app.init( function () {

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

    var form = document.forms[0];

    app.on( form, "submit", function ( e ) {
        e.preventDefault();

        var body = form.querySelector( "textarea[name=\"body\"]" ),
            subject = form.querySelector( "input[name=\"subject\"]" ),
            to_name = form.querySelector( "input[name=\"to_name\"]" ),
            to_address = form.querySelector( "input[type=\"email\"]" );

        var data = [
            {
                "name": "body",
                "value": body.value,
            },
            {
                "name": "subject",
                "value": subject.value,
            },
            {
                "name": "to_name",
                "value": to_name.value,
            },
            {
                "name": "to_address",
                "value": to_address.value,
            }
        ];

        var action = form.getAttribute( "action" );

        app.ajax( action, data, function ( response ) {
            response = JSON.parse( response );

            // TODO: modal messages..
            // TODO: sending indicator..
            if ( response.success )
            {
                toastr["success"]( l10n.messages.success );
            }
            else
            {
                toastr["error"]( l10n.messages.error );
            }
        } );

        return false;
    } );

} );
