URL = window.URL || window.webkitURL;

var gumStream;
var recorder;
var input;
var encodingType;
var encodeAfterRecord = true;
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext;
var recordButton = document.getElementById("recordButton");
var stopButton = document.getElementById("stopButton");
$('.swr-grupo .panel > .textbox > .box > .icon > .gr-moreico > ul > li.grrecord').on('click', function (e) {
    startRecording();
});


function startRecording() {
    var constraints = {
        audio: {
            sampleSize: 8,
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: false,
        }, video: false
    };
    navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
        audioContext = new AudioContext();
        gumStream = stream;
        input = audioContext.createMediaStreamSource(stream);
        encodingType = 'mp3';
        var slashes = window.location.href;
        slashes = slashes.replace($(".dumb .gdefaults > .baseurl").text(), '');
        slashes = slashes.split('/');
        slashes = slashes.length;
        var micworkerdir = '';
        for (i = 1; i < slashes; i++) {
            micworkerdir = micworkerdir+'../';
        }
        micworkerdir = micworkerdir+'riches/kit/audiorecorderjs/lib/';
        recorder = new WebAudioRecorder(input, {
            workerDir: micworkerdir,
            encoding: encodingType,
            numChannels: 2,
            onEncoderLoading: function(recorder, encoding) {},
            onEncoderLoaded: function(recorder, encoding) {
                $('.swr-grupo .panel > .textbox > .box > .icon > .gr-moreico').addClass('recording');
            }
        });

        recorder.onComplete = function(recorder, blob) {
            createDownloadLink(blob, recorder.encoding);
        };

        recorder.setOptions({
            timeLimit: 600,
            encodeAfterRecord: encodeAfterRecord,
            mp3: {
                bitRate: 192,
                mimeType: "audio/mpeg"
            }
        });
        recorder.startRecording();

    }).catch(function(err) {
        alert("Permission to use microphone denied. Kindly check Browser Settings");
        $('.gr-mic').removeClass('recrdng').fadeIn();
        $('.swr-grupo .panel > .textbox > .box > .icon > .gr-moreico').removeClass('recording');

    });
}

function stopRecording() {
    gumStream.getAudioTracks()[0].stop();
    $('.swr-grupo .panel > .textbox > .box > .icon > .gr-moreico').removeClass('recording');
    recorder.finishRecording();
}

function createDownloadLink(blob, encoding) {
    var url = URL.createObjectURL(blob);
    var filename = 'audiomsg'+ '.'+encoding;
    var data = new FormData();
    data.append("act", 1);
    data.append("do", "group");
    data.append("type", "sendaudio");
    data.append("id", "sendaudio");
    data.append("userid", $('.swr-grupo .panel > .textbox .userid').val());
    data.append("audio_data", blob, filename);
    data.append("id", $('.swr-grupo .panel').attr('no'));
    data.append("ldt", $('.swr-grupo .panel').attr('ldt'));
    data.append("from", grlastid());
    $(".swr-grupo .panel > .room > .msgs").animate({
        scrollTop: $(".swr-grupo .panel > .room > .msgs").prop("scrollHeight")
    }, 500);
    var senid = rand(8);
    var moset = $(".dumb .gdefaults").find(".sndmsgalgn").text();
    if ($('.swr-grupo .panel > .textbox .userid').val() != 0 && $('.swr-grupo .panel > .textbox .userid').val() != $(".swr-grupo .rside > .top > .left > span.vwp").attr("no")) {
        moset = $(".dumb .gdefaults").find(".rcvmsgalgn").text();
    }
    var senmsg = $(".gphrases > .sending").text();
    var msg = '<li class="you animate__animated animate__fadeIn '+senid+' '+moset+'" no="0"> <div><span class="msg"><i>';
    msg = msg+'<span class="block" type="files"><span><span class="ptxt">'+(escapeHtml(senmsg))+'</span><span class="animate__animated animate__fadeInUp animate__infinite">';
    msg = msg+'<i class="gi-upload"></i></span></span></span></i>';
    msg = msg+'</span></div></li>';
    $('.swr-grupo .panel > .room > .msgs').append(msg);
    scrollmsgs();
    $.ajax({
        url: '',
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        async: true,
        data: data,
        type: 'post',
    }).done(function(data) {
        data = $.parseJSON(data);
        $("."+senid).remove();
        if ($(".swr-grupo .panel").attr("no") == data[0].gid) {
            loadmsg(data);
        }
    }).fail(function() {
        $(".swr-grupo .panel > .room > .msgs > li."+senid+" > div > .msg > i > span.block > span > span.ptxt").text($(".gphrases > .failed").text());
        $(".swr-grupo .panel > .room > .msgs > li."+senid+" > div > .msg > i > span.block > span > span > i").removeClass("gi-upload");
        $(".swr-grupo .panel > .room > .msgs > li."+senid+" > div > .msg > i > span.block > span > span").removeClass("animate__animated");
        $(".swr-grupo .panel > .room > .msgs > li."+senid+" > div > .msg > i > span.block > span > span > i").addClass("gi-minus-circled-1");
        setTimeout(function() {
            $("."+senid).remove();
        }, 2000);
    })
}