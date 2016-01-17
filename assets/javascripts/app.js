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

/* --- */

app_init( function () {

    //

} );
