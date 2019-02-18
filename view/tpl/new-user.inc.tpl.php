<div class="row">
    <div class="col-100">
        <label class="welcome_text">Welcome to the Dynamic Quiz</label>
        <br/>
        <label id='welcomeTxt' class="subtext">Please enter your name</label>
    </div>
</div>
<div class="row">
    <div class="col-100">
        &nbsp;
    </div>
</div>
<div class="row">
    <div class="col-25">
        <label for="fname">Name</label>
    </div>
    <div class="col-75">
        <input type="text" id="username" name="firstname" placeholder="Your Name or Username..">
    </div>
</div>
<div class="row">
    <div class="col-100">
        &nbsp;
    </div>
</div>
<div class="row">
    <div class="col-100">
        <input type="submit" class="controlButton" value="Next" onclick="submitUser()">
    </div>
</div>
<script language="JavaScript">
    document.getElementById('username').focus();
</script>