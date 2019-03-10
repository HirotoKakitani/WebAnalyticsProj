var url = "http://localhost:8000/api/log.php";
//var url = "http://167.99.172.17:8080/api/log.php";

//sessionization
if (document.cookie.split(';').filter((item) => item.trim().startsWith('sessionId=')).length) {
    //check if sessionId cookie is set
    var match = document.cookie.match(new RegExp('(^| )' + 'sessionId' + '=([^;]+)'));
    if (match)
        var sessionId = match[2];
    console.log('The cookie exists');
}
//issue a cookie
else{
    var sessionId = Math.random().toString(36).substr(2,16);
    document.cookie = "sessionId="+sessionId;
    console.log (sessionId);
}
//records static information
window.addEventListener('load',function(){
    console.log("Collector script loaded!");
    //console.log(window.screen);
    //console.log(window.navigator);
    var collectedWidth = window.screen.width;
    var collectedHeight = window.screen.height;
    var collectedDevice = window.navigator.userAgent;
    console.log("Resolution: " + collectedWidth + " x " + collectedHeight);
    console.log("Browser and device: " + collectedDevice); 
    var timingData = window.performance.timing;
    console.log(timingData);
    //var totalLoadTime = timingData.loadEventEnd - timingData.navigationStart; //total page load
    var connectTime =  timingData.responseEnd - timingData.requestStart;   //connection time
    var renderTime = timingData.domComplete - timingData.domLoading;    //DOM render time
    //console.log("Total page load time : " + totalLoadTime);
    console.log("Connect time: " + connectTime);
    console.log("Render time: " + renderTime);
    
    console.log("load session: " + sessionId);

    var data = new FormData();
    //console.log("collectedDevice :" + typeof String(collectedDevice));
    //console.log("collectedDevice :" + String(collectedDevice));
    data.append('id', sessionId);
    data.append('w', String(collectedWidth));
    data.append('h', String(collectedHeight));
    data.append('ct', String(connectTime));
    data.append('rt', String(renderTime));
    data.append('d', String(collectedDevice));
    data.append('t','load');    

    //var data = "test=testing!";
    //for (var i of data){
    //    console.log(i);
    //}
    var res = navigator.sendBeacon(url,data);
    console.log("res: " + res);
});

//records errors
window.addEventListener('error',function(event){
    var errorMessage = event.message;
    var errorTimeStamp = event.timeStamp;
    console.log("Error: " + errorMessage + " at time " + errorTimeStamp);    
    console.log("error session: " + sessionId);
    var data = new FormData();
    //console.log("collectedDevice :" + typeof String(collectedDevice));
    //console.log("collectedDevice :" + String(collectedDevice));
    data.append('id', sessionId);
    data.append('e', String(errorMessage));
    data.append('ti', String(errorTimeStamp));
    data.append('t','error');    

    //var data = "test=testing!";
    //for (var i of data){
    //    console.log(i);
    //}
    var res = navigator.sendBeacon(url,data);
    console.log("res: " + res);


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
    //console.log("Scrolled " + scrollTarget + " at " + scrollTime);
    
},true);
