function getSetInd(cSettore) {
    var url = window.basePATH + 'isIndustriaGet.php?cod=' + encodeURIComponent(cSettore);
    var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;

    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    return httpXml.responseText;
}