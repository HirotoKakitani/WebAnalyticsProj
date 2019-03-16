window.onload = function(){
    var editButtons = document.getElementsByClassName('edit');
    var modal = document.getElementById('modalBox');
    var closeButton = document.getElementById('close'); 
    var delButton = document.getElementById('delete');
    var saveButton = document.getElementById('save');
    
    var hidden = document.getElementById('hiddenField');
    
    closeButton.addEventListener('click',function(){
        location.reload();  
    });

    //send del request
    delButton.addEventListener('click',function(){
        var param = this.parentNode.childNodes[1].value;
        console.log(param); 
        var req = new XMLHttpRequest();
        //req.addEventListener("load", reqListener);
        req.open("DELETE", "userManageAPI.php");
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
        req.send("u="+encodeURIComponent(param));
    });
   
    //send put request
    saveButton.addEventListener('click', function(){
        console.log('saving');
        var param1 = this.parentNode.childNodes[1].value;
        var param2 = this.parentNode.childNodes[3].checked;
        var param3 = hidden.value;
        console.log(param1 + param2+param3); 
        var req = new XMLHttpRequest();
        //req.addEventListener("load", reqListener);
        req.open("PUT", "userManageAPI.php");
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
        req.send("u="+encodeURIComponent(param1)+"&a="+encodeURIComponent(param2)+"&o="+encodeURIComponent(param3));
    
    });

    for (var i = 0; i < editButtons.length; i++) {
        editButtons[i].addEventListener('click', showEditBox(editButtons[i].parentNode.parentNode.childNodes, modal), false);
    }   
    
}

function showEditBox(elem, modal){
    return function (){
        document.getElementById('modalText').value=elem[1].innerHTML;
        document.getElementById('modalCheck').checked = elem[3].innerHTML == 'true'?true:false;
        document.getElementById('hiddenField').value = elem[1].innerHTML;
        modal.style.display="block";
        console.log(elem[1].innerHTML);
        console.log(elem[3].innerHTML);
    }
}

