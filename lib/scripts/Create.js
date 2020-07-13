function RegisterUser(){
    
    var Registation = {email: document.forms["account"]["email"].value,
                        phone: document.forms["account"]["phone"].value,
                        password: document.forms["account"]["password"].value,
                        firstname: document.forms["account"]["firstname"].value,
                        lastname: document.forms["account"]["lastname"].value,
                        birthdate: document.forms["account"]["birthdate"].value};
    

    if (Registation.email !== "") {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "createuser.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/json");
        xmlhttp.onreadystatechange = function ()
        {if (this.readyState === 4){
            setStatus(xmlhttp.responseText, this.status);
        }};
        var sendval = JSON.stringify(Registation);
        xmlhttp.send(sendval);
    }
    else{
        document.getElementById('emailerror').innerHTML = "Email must be filled out";
    }
}

function setStatus(response, errorCode){
    var Retval = JSON.parse(response);

    var head = document.getElementById('statustext');  
    head.innerHTML = Retval.messages[0]

    if(errorCode===200){
        setStatusGreen();
    }else{
        setStatusRed();
    }
}

function setStatusGreen(){
    var div = document.getElementById('status');         
    div.style.backgroundColor='green';
    div.style.display='block';
    var head = document.getElementById( 'statushead' );  
    head.innerHTML = "User Created"
}

function setStatusRed(){
    var div = document.getElementById( 'status' );         
    div.style.backgroundColor='red';
    div.style.display='block';
    var head = document.getElementById( 'statushead' );  
    head.innerHTML = "Error Creating User"
}

function resetStatus(){
    var div = document.getElementById( 'status' );         
    div.style.display='none';
}


function formchanged(){

    var eMail = {email: document.forms["account"]["email"].value};
  

    if (eMail !== "") {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "userexist.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/json");
        xmlhttp.onreadystatechange = function ()
        {if (this.readyState === 4){
            VaildateEmail(xmlhttp.responseText);
        }};
        var sendval = JSON.stringify(eMail);
        xmlhttp.send(sendval);
    }
    else{
        document.getElementById('emailerror').innerHTML = "Email must be filled out";
    }

    VaildateFirstName();
    VaildateLastName();
    VaildatePassword();
    VaildatePhone();
    VaildateBirthdate()
}

function VaildateEmail(response){

    var Retval = JSON.parse(response);

    var eMail = document.forms["account"]["email"].value;
    if (eMail === "") {
        document.getElementById('emailerror').innerHTML = "Email must be filled out";
    }
    else{
        if (Retval.statusCode === 200){
            if(Retval.data.emailverified){
                document.getElementById('emailerror').innerHTML = "Email exist"; 
            }else{
                document.getElementById('emailerror').innerHTML = ""; 
            }
        }else{
            document.getElementById('emailerror').innerHTML = Retval.messages[0]; 
        }
    }
}

function VaildateFirstName(){
    var firstname = document.forms["account"]["firstname"].value;
    if (firstname === "") {
        document.getElementById('firstnameerror').innerHTML = "First name must be filled out";
    }else{
        document.getElementById('firstnameerror').innerHTML = "";
    }
}
function VaildateLastName(){
    var lastname = document.forms["account"]["lastname"].value;
    if (lastname === "") {
        document.getElementById('lastnameerror').innerHTML = "Last name must be filled out";
    }else{
        document.getElementById('lastnameerror').innerHTML = "";
    }
}
function VaildatePassword(){
    var password = document.forms["account"]["password"].value;
    if (password === "") {
        document.getElementById('passworderror').innerHTML = "Password must be filled out";
    }else{
        document.getElementById('passworderror').innerHTML = "";
    }
}

function VaildatePhone(){
    var phone = document.forms["account"]["phone"].value;
    if (phone === "") {
        document.getElementById('phoneerror').innerHTML = "Phone must be filled out";
    }else{
        document.getElementById('phoneerror').innerHTML = "";
    }
}
function VaildateBirthdate(){
    var birthdate = document.forms["account"]["birthdate"].value;
    if (birthdate === "") {
        document.getElementById('birthdateerror').innerHTML = "Birthdate must be filled out";
    }else{
        document.getElementById('birthdateerror').innerHTML = "";
    }
}