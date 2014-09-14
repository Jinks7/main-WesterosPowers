function ajaxRequest(){
    var activexmodes=["Msxml2.XMLHTTP", "Microsoft.XMLHTTP"] //activeX versions to check for in IE
    if (window.ActiveXObject){ //Test for support for ActiveXObject in IE first (as XMLHttpRequest in IE7 is broken)
        for (var i=0; i<activexmodes.length; i++){
            try{
                return new ActiveXObject(activexmodes[i])
            }
            catch(e){
            //suppress error
            }
        }
    }
    else if (window.XMLHttpRequest) // if Mozilla, Safari etc
        return new XMLHttpRequest()
    else
        return false
}

function getAjaxPage(url, callback){
    
    var request = new ajaxRequest();
    
    request.onreadystatechange = function(){
        if (request.readyState == 4){
                if (request.status == 200){
                    document.getElementById("body").innerHTML = request.responseText;
                    document.title = document.getElementById("body").getElementsByTagName("title")[0].innerHTML;
                    //window.history.pushState({"html":request.,"pageTitle":request.pageTitle},"", url);
                } else {
                    alert("Sorry there was an error.");
                }
            }
    };
    
    request.open("GET", url, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
}