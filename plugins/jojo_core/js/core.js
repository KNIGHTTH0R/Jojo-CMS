

/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure) {
    document.cookie= name + "=" + escape(value) + ((expires) ? "; expires=" + expires.toGMTString() : "") +((path) ? "; path=" + path : "") +((domain) ? "; domain=" + domain : "") +((secure) ? "; secure" : "");
}

/**
 * Gets the value of the specified cookie.
 *
 * name  Name of the desired cookie.
 *
 * Returns a string containing value of specified cookie,
 *   or null if cookie does not exist.
 */
function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    } else {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

/**
 * Deletes the specified cookie.
 *
 * name      name of the cookie
 * [path]    path of the cookie (must be same as path used to create cookie)
 * [domain]  domain of the cookie (must be same as domain used to create cookie)
 */
function deleteCookie(name, path, domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") +"; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}

function isNull(a) {
  return typeof a == 'object' && !a;
}

function nl2br(myString){
  return myString.replace( /\n/g, '<br />\n' );
}

function xyz(c,a,b,s) {
    var s = (s == null) ? true : s;
    var o = '';
    var m = '';
    var m2 = ':otliam';
    for (i = 0; i <= b.length; i++) {o = b.charAt (i) + o;}
    b = o;
    for (i = 0; i <= m2.length; i++) {m = m2.charAt (i) + m;}
    if (!s) {m = '';}
    return m + a + unescape('%'+'4'+'0') + b + '.' + c;
}
