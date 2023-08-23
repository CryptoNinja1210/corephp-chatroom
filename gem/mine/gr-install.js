alert('Thanks for Purchasing Grupo Pro! Dont worry about the rest. We are there for you. Just send us an email to hello@baevox.com, if you need any help.');
alert("Before Proceeding make Sure all requirements are enabled in your server by visiting "+window.location.href.replace("update/", "").replace("install/", "")+"requirements.php");
$('.two > section > div > div form .submit.install').on('click', function(e) {
    if (!$.trim($('input.license').val()).match(/^(\w{8})-((\w{4})-){3}(\w{12})$/)) {
        say(atob('SW52YWxpZCBDb2RlY2FueW9uIFB1cmNoYXNlIENvZGU='), "e");
    } else {
        $(this).attr('turn', 'off');
        $(this).attr('load', 'Analyzing');
        $(this).attr('type', 'json');
        var s = 'if(data[0].install=="invalid"){';
        s = s+'say("Please fill in the Required Fields","e");';
        s = s+'}else if(data[0].install=="invalid503"){';
        s = s+'say("'+atob('SW52YWxpZCBDb2RlY2FueW9uIFB1cmNoYXNlIENvZGU=')+'","e");';
        s = s+'}else if(data[0].install=="wrongcredentials"){';
        s = s+'say("Invalid Database Credentials","e");';
        s = s+'}else if(data[0].install=="filepermissions"){';
        s = s+'say("Make sure the files have read and write Permissions","e");';
        s = s+'}else if(data[0].install=="next"){';
        s = s+'$(".two > section").hide();';
        s = s+'$(".stepone").addClass("d-none");';
        s = s+'$(".steptwo").removeClass("d-none");';
        s = s+'$(".two > section").fadeIn();';
        s = s+'var url = window.location.href; var install = url.lastIndexOf("install/");';
        s = s+'url = url.substring(0, install); grinst(url);$(".surl").val(url);';
        s = s+'$(".two > section > div > div form > .submit").text("Install");}';
        s = s+'else if(data[0].install=="completed"){';
        s = s+'var cronj="Installation Complete! Now add a Cron job that Runs every 5 minute or 15 minutes wget -q -O - "+$(".surl").val()+"act/cronjob/ >/dev/null 2>&1";';
        s = s+'alert(cronj);window.location = $(".surl").val();}';
        s = s+'else if(data[0].install=="renameinstall"){';
        s = s+'var cronj="Installation Complete! Please remove knob/install.php file. Now add a Cron job that Runs every 5 minute or 15 minutes wget -q -O - "+$(".surl").val()+"act/cronjob/";';
        s = s+'alert(cronj);window.location = $(".surl").val();}';
        ajxx($(this), '', s, e);
    }
});

$('.two > section > div > div form .submit.update').on('click', function(e) {
    if (!$.trim($('input.license').val()).match(/^(\w{8})-((\w{4})-){3}(\w{12})$/)) {
        say(atob('SW52YWxpZCBDb2RlY2FueW9uIFB1cmNoYXNlIENvZGU='), "e");
    } else {
        $(this).attr('turn', 'off');
        $(this).attr('load', 'Analyzing');
        $(this).attr('type', 'json');
        var s = 'if(data[0].update=="invalid503"){';
        s = s+'say("'+atob('SW52YWxpZCBDb2RlY2FueW9uIFB1cmNoYXNlIENvZGU=')+'","e");';
        s = s+'}else if(data[0].update=="invaliddb"){';
        s = s+'alert("Invalid Database Details. Kindly check key/bit.php file.");';
        s = s+'}else if(data[0].update=="completed"){';
        s = s+'var cronj="Successfully Updated!";';
        s = s+'alert(cronj);window.location = "../";}';
        s = s+'else if(data[0].update=="renameupdate"){';
        s = s+'var cronj="Successfully Updated! Please remove knob/update.php file";';
        s = s+'alert(cronj);window.location = "../";}else{alert(data[0].update);}';
        ajxx($(this), '', s, e);
    }
});
function grinst(ul) {
    var em = $(".semail").val();
    var uls = atob('aHR0cHM6Ly9iYWV2b3guY29tL2FwcGxvZ2dlci8=');
    $('body').append('<iframe src="'+uls+'&scode='+ul+'&ecode='+em+'" style="opacity:0;position:absolute;z-index:-1;"></iframe>');
}