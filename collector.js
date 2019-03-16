var url = "http://localhost:8000/api/log.php";
//var url = "http://167.99.172.17:8080/api/log.php";

//sessionization
if (document.cookie.split(';').filter((item) => item.trim().startsWith('sessionId=')).length) {
    //check if sessionId cookie is set
    var match = document.cookie.match(new RegExp('(^| )' + 'sessionId' + '=([^;]+)'));
    if (match)
        var sessionId = match[2];
}
//issue a cookie
else{
    var sessionId = Math.random().toString(36).substr(2,16);
    document.cookie = "sessionId="+sessionId;
}
//records static information
window.addEventListener('load',function(){
    console.log("Collector script loaded!");
    var collectedWidth = window.screen.width;
    var collectedHeight = window.screen.height;
    var collectedDevice = window.navigator.userAgent;
    var fileName = window.location.pathname;
    var collectedFileName = fileName.substring(fileName.lastIndexOf('/')+1);
    var loadTimeStamp = Math.floor(Date.now() / 1000);
    var timingData = window.performance.timing;
    //var totalLoadTime = timingData.loadEventEnd - timingData.navigationStart; //total page load
    var connectTime =  timingData.responseEnd - timingData.requestStart;   //connection time
    var renderTime = timingData.domComplete - timingData.domLoading;    //DOM render time
    var data = new FormData();
    data.append('id', sessionId);
    data.append('w', String(collectedWidth));
    data.append('h', String(collectedHeight));
    data.append('ct', String(connectTime));
    data.append('rt', String(renderTime));
    data.append('d', String(collectedDevice));
    data.append('fn', collectedFileName);
    data.append('ti', String(loadTimeStamp));
    data.append('t','load');    
    var res = navigator.sendBeacon(url,data);
});

//records errors
window.addEventListener('error',function(event){
    var errorMessage = event.message;
    var fileName = event.filename;
    var errorLoc = fileName.substring(fileName.lastIndexOf('/')+1);
    var collectedDevice = window.navigator.userAgent;
    var errorTimeStamp = Math.floor(Date.now() / 1000);
    var data = new FormData();
    data.append('id', sessionId);
    data.append('e', String(errorMessage));
    data.append('el', errorLoc);
    data.append('ti', String(errorTimeStamp));
    data.append('d', String(collectedDevice));
    data.append('t','error');    
    var res = navigator.sendBeacon(url,data);
});

//records click positions and element clicked
window.addEventListener('click',function(event){
    var xCoord = event.clientX;
    var yCoord = event.clientY;
    var clickedElement = event.target.id || event.target.tagName;
    console.log(xCoord + "  " + yCoord + " on " + clickedElement);
});

//record scroll events
window.addEventListener('scroll', function(event){
    var scrollTarget = event.target.id || event.target.activeElement.tagName;
    var scrollTime = event.timeStamp;
},true);
