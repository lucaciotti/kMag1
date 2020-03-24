if (!String.prototype.trim) {
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    }
}

window.basePATH = window.location.protocol + "//" + window.location.host + "/kMag2/src/ajaxUtils/";
var httpXml = false;
var makeHttpXml = function () {
    "use strict";
    httpXml = false;
    if (window.XMLHttpRequest) { // Mozilla, Safari,...
        httpXml = new XMLHttpRequest();
    } else if (window.ActiveXObject) { // IE
        try {
            httpXml = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e1) {
            try {
                httpXml = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e2) { }
        }
    }
    if (!httpXml) {
        alert('Cannot create XMLHTTP instance');
        return false;
    }
};

var sendUrl = function (url) {
    "use strict";
    makeHttpXml();
    httpXml.open("GET", url, true);
    httpXml.send(null);
};

function htmlEntities(str) {
    return String(str).replace('&amp;', "&");
}

var noCache = function () {
    var milliseconds = (new Date()).getTime();
    return "&x=" + milliseconds;
};

// --------------------------------

var senduser = function (id_testa) {
    "use strict";
    var user = document.getElementById("user" + id_testa).value;
    var url = "writeuser.php?id=" + id_testa + "&user=" + user;
    sendUrl(url);
};

var soloNumeri = function (id) {
    var valore = document.getElementById(id).value
    valore = valore.replace(/[^\d]/g, '')
    document.getElementById(id).value = valore
};
