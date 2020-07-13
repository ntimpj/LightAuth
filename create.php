<html>
<head>
    <script src="lib/scripts/Create.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/scripts/Create.css">
</head>  
<body>
    <div id="signup" class="signup">
        <form id="account" class="account">
            <fieldset>

                <legend>Personal Information:</legend>
    
                <div id="emailgroup" class=inputgroup>
                    <label id="emaillable" class="inputlable" for="email">E-Mail adress:</label>
                    <input id="email" class="inputfield" type="email" name="email" value="" onfocusout="formchanged()" placeholder="E-Mail" autocomplete="on" required autofocus>
                    <label id="emailerror" class="inputerror" for="email"></label>
                </div>

                <div id="firstnamegroup" class=inputgroup>
                    <label id="firtnamelable" class="inputlable" for="firstname">First name:</label>
                    <input id="firstname" class="inputfield" type="text" name="firstname" value="" onfocusout="formchanged()" placeholder="First Name(s)" autocomplete="on" required>
                    <label id="firstnameerror" class="inputerror" for="firstname"></label>
                </div>

                <div id="lastnamegroup" class=inputgroup>
                    <label id="lastnamelable" class="inputlable" for="lastname">Last name:</label>
                    <input id="lastname" class="inputfield" type="text" name="lastname" value="" onfocusout="formchanged()" placeholder="Last Name" autocomplete="on" required>
                    <label id="lastnameerror" class="inputerror" for="lastname"></label>
                </div>

                <div id="passwordgroup" class=inputgroup>
                    <label id="passwordlable" class="inputlable" for="password">Password:</label>
                    <input id="password" class="inputfield" type="password" name="password" value="" onfocusout="formchanged()" placeholder="Password" autocomplete="on" required>
                    <label id="passworderror" class="inputerror" for="password"></label>
                </div>

                <div id="phonegroup" class=inputgroup>
                    <label id="phonelable" class="inputlable" for="phone">Phone number:</label>
                    <input id="phone" class="inputfield" type="text" name="phone" value="" onfocusout="formchanged()" placeholder="Phone number" autocomplete="on" required>
                    <label id="phoneerror" class="inputerror" for="phone"></label>
                </div>

                <div id="birthdategroup" class=inputgroup>
                    <label id="birthdatelable" class="inputlable" for="birthdate">Birthdate:</label>
                    <input id="birthdate" class="inputfield" type="date" name="birthdate" value="" onfocusout="formchanged()" placeholder="Birth date" autocomplete="on" required>  
                    <label id="birthdateerror" class="inputerror" for="birthdate"></label>
                </div>

                <div id="gendergroup" class=inputgroup>
                    <label id="genderlable" class="inputlable" for="gender">Gender:</label>
                    <select id="gender" class="inputfield" name="gender" onfocusout="formchanged()" >
                        <option value="none"></option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
            </fieldset>
        </form>
        <button id="registerbutton" class="button" onclick="RegisterUser()">Register</button>
    </div>
    <div id="status" class="status">
        <div id="statushead" class="statushead">Test Head</div>
        <div id="statustext" class="statustext">New user created</div>
        <button id="statusbutton" class="button" onclick="resetStatus()">OK</button>
    </div>

</body>
</html>