//TODO set up endpoint on server
//TODO package and send to endpoint
//TODO save to database.
//TODO generate report
//TODO authentication

//records static information
window.addEventListener('load',function(){
    console.log("Collector script loaded!");
    //console.log(window.screen);
    //console.log(window.navigator);
    var collectedWidth = window.screen.width;
    var collectedHeight = window.screen.height;
    var collectedDevice = window.navigator.appVersion;
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

});

//records errors
window.addEventListener('error',function(event){
    var errorMessage = event.message;
    var errorTimeStamp = event.timeStamp;
    console.log("Error: " + errorMessage + " at time " + errorTimeStamp);    
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
    console.log("Scrolled " + scrollTarget + " at " + scrollTime);
    
},true);
