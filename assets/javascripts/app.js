/* app.js */

/**
 * Initialize the app.
 *
 * @param mixed callback
 *
 * @return void
 */
function app_init( callback ) {
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
 * @param mixed element
 * @param string name
 * @param mixed callback
 *
 * @return void
 */
function app_add_event_listener( element, name, callback ) {
    if ( element.addEventListener ) {
        element.addEventListener( name, callback );
    } else {
        element.attachEvent("on" + name, function () {
            callback.call( element );
        } )
    }
}

function app_send_post_data( url, data, callback ) {
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

app_init( function () {

    var form = document.forms[0];

    app_add_event_listener( form, "submit", function ( e ) {
        e.preventDefault();

        var name = form.querySelector( "input[name=\"name\"]" ),
            e_mail = form.querySelector( "input[type=\"email\"]" ),
            message = form.querySelector( "textarea[name=\"message\"]" );

        var data = [
            {
                "name": "name",
                "value": name.value,
            },
            {
                "name": "e-mail-address",
                "value": e_mail.value,
            },
            {
                "name": "message",
                "value": message.value,
            }
        ];

        var action = form.getAttribute( "action" );

        app_send_post_data( action, data, function ( response ) {
            //
        } );

        return false;
    } );

} );
