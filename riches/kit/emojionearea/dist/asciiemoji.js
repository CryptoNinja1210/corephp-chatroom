function asciiemoji(str) {
    $emoji = 0;
    if ($(".dumb .gdefaults").find(".asciismileys").text() == "enable") {
        var emojis = {
            "&lt;3": ":heart:", "&lt;3": ":heart:", "&lt;/3": ":broken_heart:", ":')": ":joy:", ":'-)": ":joy:", ":D": ":smiley:", ":-D": ":smiley:", "=D": ":smiley:",
            ":)": ":slight_smile:", ":-)": ":slight_smile:", "=]": ":slight_smile:", "=)": ":slight_smile:",
            ":]": ":slight_smile:", "':)": ":sweat_smile:", "':-)": ":sweat_smile:", "'=)": ":sweat_smile:", "':D": ":sweat_smile:",
            "':-D": ":sweat_smile:", "&gt;:)": ":laughing:", "&gt;;)": ":laughing:", "&gt;:-)": ":laughing:", "&gt;=)": ":laughing:",
            ";)": ":wink:", ";-)": ":wink:", "*-)": ":wink:", "*)": ":wink:", ";-]": ":wink:", ";]": ":wink:", ";D": ":wink:", ";^)": ":wink:",
            "':(": ":sweat:", "':-(": ":sweat:", "'=(": ":sweat:", ":*": ":kissing_heart:", ":-*": ":kissing_heart:", "=*": ":kissing_heart:",
            ":^*": ":kissing_heart:", "&gt;:P": ":stuck_out_tongue_winking_eye:", "X-P": ":stuck_out_tongue_winking_eye:",
            "x-p": ":stuck_out_tongue_winking_eye:", "&gt;:[": ":disappointed:", ":-(": ":disappointed:", ":(": ":disappointed:",
            ":-[": ":disappointed:", ":[": ":disappointed:", "=(": ":disappointed:", "&gt;:(": ":angry:",
            "&gt;:-(": ":angry:", ":@": ":angry:", ":'(": ":cry:", ":'-(": ":cry:", ";(": ":cry:", ";-(": ":cry:", "&gt;.&lt;": ":persevere:",
            "D:": ":fearful:", ":$": ":flushed:", "=$": ":flushed:", "#-)": ":dizzy_face:", "#)": ":dizzy_face:", "%-)": ":dizzy_face:",
            "%)": ":dizzy_face:", "X)": ":dizzy_face:", "X-)": ":dizzy_face:", "*\0/*": ":ok_woman:", "\0/": ":ok_woman:",
            "\O/": ":ok_woman:", "O:-)": ":innocent:", "0:-3": ":innocent:", "0:3": ":innocent:", "0:-)": ":innocent:",
            "0:)": ":innocent:", "0;^)": ":innocent:", "O:-)": ":innocent:", "O:)": ":innocent:", "O;-)": ":innocent:", "O=)": ":innocent:",
            "0;-)": ":innocent:", "O:-3": ":innocent:", "O:3": ":innocent:", "B-)": ":sunglasses:", "B)": ":sunglasses:", "8)": ":sunglasses:",
            "8-)": ":sunglasses:", "B-D": ":sunglasses:", "8-D": ":sunglasses:", "-_-": ":expressionless:", "-__-": ":expressionless:",
            "-___-": ":expressionless:", "&gt;:/": ":confused:", ":-/": ":confused:", ":-.": ":confused:",
            "=/": ":confused:", ":L": ":confused:",
            ":P": ":stuck_out_tongue:", ":-P": ":stuck_out_tongue:", ":-p": ":stuck_out_tongue:", ":-O": ":open_mouth:", ":O": ":open_mouth:", ":-o": ":open_mouth:",
            "O_O": ":open_mouth:", "&gt;:O": ":open_mouth:", ":-X": ":no_mouth:", ":X": ":no_mouth:", ":-#": ":no_mouth:",
            ":#": ":no_mouth:", ":x": ":no_mouth:", ":-x": ":no_mouth:",
        };
        for (var key in emojis) {
            if (emojis.hasOwnProperty(key)) {
                var rp = key.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                rp = '(^|\\s)'+rp+'($|\\s)';
                var re = new RegExp(rp, 'g');
                str = str.replace(re, ' '+emojis[key]+' ');
            }
        }
    }
    return str;
}