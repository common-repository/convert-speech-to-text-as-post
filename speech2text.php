<?php function easyspeech2text_convertpost() { ?>
<!DOCTYPE html>
<meta charset="utf-8">
<title>SPEECH TO TEXT CONVERTER</title>
<style>
  a:link {
    color:#000;
    text-decoration: none;
  }
  a:visited {
    color:#000;
  }
  a:hover {
    color:#33F;
  }
  .center {
    padding: 10px;
    text-align: center;
  }
  .final {
    color: black;
    padding-right: 3px; 
  }
  .interim {
    color: gray;
  }
  .info {
    font-size: 14px;
    text-align: center;
    color: #777;
    display: block;
  }
  .right {
    float: right;
  }
  .sidebyside {
    display: inline-block;
    width: 45%;
    min-height: 40px;
    text-align: left;
    vertical-align: top;
  }
  #headline {
    font-size: 40px;
    font-weight: 300;
  }
  #info {
    font-size: 20px;
    text-align: center;
    color: #777;
    visibility: hidden;
  }
  #results {
    font-size: 14px;
    font-weight: bold;
    border: 1px solid #ddd;
    padding: 15px;
    text-align: left;
    min-height: 150px;
  }
  #start_button {
    border: 0;
    background-color:transparent;
    padding: 0;
  }
</style>
<h1 class="center" id="headline">
  SPEECH TO TEXT CONVERTER
  </h1>
<div id="info">
  <p id="info_start">Click on the microphone icon and begin speaking.</p>
  <p id="info_speak_now">Speak now.</p>
  <p id="info_no_speech">No speech was detected. You may need to adjust your
    <a href="//support.google.com/chrome/bin/answer.py?hl=en&amp;answer=1407892">
      microphone settings</a>.</p>
  <p id="info_no_microphone" style="display:none">
    No microphone was found. Ensure that a microphone is installed and that
    <a href="//support.google.com/chrome/bin/answer.py?hl=en&amp;answer=1407892">
    microphone settings</a> are configured correctly.</p>
  <p id="info_allow">Click the "Allow" button above to enable your microphone.</p>
  <p id="info_denied">Permission to use microphone was denied.</p>
  <p id="info_blocked">Permission to use microphone is blocked. To change,
    go to chrome://settings/contentExceptions#media-stream</p>
  <p id="info_upgrade">Web Speech API is not supported by this browser.
     Upgrade to <a href="//www.google.com/chrome">Chrome</a>
     version 25 or later.</p>
</div>
<div class="right">
  <button id="start_button" onclick="startButton(event)">
    <img id="start_img" src="<?php echo plugins_url('/images/mic.gif',__FILE__); ?>" alt="Start"></button>
</div>
<div id="results">
  <span id="final_span" class="final"></span>
  <span id="interim_span" class="interim"></span>
  <p>
</div>
<div class="center">
  <div class="sidebyside" style="text-align:right">
    
    <div id="copy_info" class="info">
      Press Control+C to copy text.<br>
    </div>
	 <div class="info">
     Upload an audio file to convert.<br>
    </div>
	<input type="file" id="audio" onchange="playFile(this)" />
    <audio id="sound" controls></audio>
  </div>

</div>
<script>
jQuery(document).ready(function(){
    var audio= document.getElementById("sound");
    audio.addEventListener('ended', function(){
        jQuery( "#start_button" ).trigger( "click" );
        });
     audio.addEventListener('playing', function(){
 jQuery( "#start_button" ).trigger( "click" );
});   
    jQuery("#start_button").click(function(e){
    e.preventDefault()
});
});
showInfo('info_start');
var final_transcript = '';
var recognizing = false;
var ignore_onend;
var start_timestamp;
if (!('webkitSpeechRecognition' in window)) {
  upgrade();
} else {
  start_button.style.display = 'inline-block';
  var recognition = new webkitSpeechRecognition();
  recognition.continuous = true;
  recognition.interimResults = true;

  recognition.onstart = function() {
     
    recognizing = true;
    showInfo('info_speak_now');
     start_img.src = '<?php echo plugins_url('/images/mic-animate.gif',__FILE__); ?>';
  };

  recognition.onerror = function(event) {
    if (event.error == 'no-speech') {
      start_img.src = '<?php echo plugins_url('/images/mic.gif',__FILE__);?>';
      showInfo('info_no_speech');
      ignore_onend = true;
    }
    if (event.error == 'audio-capture') {
      start_img.src = '<?php echo plugins_url('/images/mic.gif',__FILE__); ?>';
      showInfo('info_no_microphone');
      ignore_onend = true;
    }
    if (event.error == 'not-allowed') {
      if (event.timeStamp - start_timestamp < 100) {
        showInfo('info_blocked');
      } else {
        showInfo('info_denied');
      }
      ignore_onend = true;
    }
  };

  recognition.onend = function() {
      var content=jQuery("#final_span").html();
      var added_vaule = tinyMCE.activeEditor.getContent({format : 'text'});
      var content1=tinyMCE.activeEditor.getContent({format : 'raw'});
      if(added_vaule.length == '1'){
          var new_content=content;
      }else
      {
          var new_content=content1+" "+content;
      }
      
      tinyMCE.activeEditor.setContent(new_content);
    recognizing = false;
    if (ignore_onend) {
      return;
    }
    start_img.src = '<?php echo plugins_url('/images/mic.gif',__FILE__);?>';
    if (!final_transcript) {
      showInfo('info_start');
      return;
    }
    showInfo('');
    if (window.getSelection) {
      window.getSelection().removeAllRanges();
      var range = document.createRange();
      range.selectNode(document.getElementById('final_span'));
      window.getSelection().addRange(range);
    }
  };

  recognition.onresult = function(event) {
    var interim_transcript = '';
    for (var i = event.resultIndex; i < event.results.length; ++i) {
      if (event.results[i].isFinal) {
        final_transcript += event.results[i][0].transcript;
      } else {
        interim_transcript += event.results[i][0].transcript;
      }
    }
    final_transcript = capitalize(final_transcript);
    final_span.innerHTML = linebreak(final_transcript);
    interim_span.innerHTML = linebreak(interim_transcript);
    if (final_transcript || interim_transcript) {
      showButtons('inline-block');
    } 
  };
}

function upgrade() {
  start_button.style.visibility = 'hidden';
  showInfo('info_upgrade');
}
var two_line = /\n\n/g;
var one_line = /\n/g;
function linebreak(s) {
  return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
}
var first_char = /\S/;
function capitalize(s) {
  return s.replace(first_char, function(m) { return m.toUpperCase(); });
}

function startButton(event) {
  if (recognizing) {
    recognition.stop();
    return;
  }
  final_transcript = '';
  //recognition.lang = select_dialect.value;
  recognition.start();
  ignore_onend = false;
  final_span.innerHTML = '';
  interim_span.innerHTML = '';
  start_img.src = '<?php echo plugins_url('/images/mic-slash.gif',__FILE__);?>';
  showInfo('info_allow');
  showButtons('none');
  start_timestamp = event.timeStamp;
}
function showInfo(s) {
  if (s) {
    for (var child = info.firstChild; child; child = child.nextSibling) {
      if (child.style) {
        child.style.display = child.id == s ? 'inline' : 'none';
      }
    }
    info.style.visibility = 'visible';
  } else {
    info.style.visibility = 'hidden';
  }
}
var current_style;
function showButtons(style) {
  if (style == current_style) {
    return;
  }
  current_style = style;
}
function playFile(obj) {
    
  var sound = document.getElementById('sound');
  var reader = new FileReader();
  reader.onload = (function(audio) {return function(e) {audio.src = e.target.result;};})(sound);
  reader.addEventListener('load', function() {
    document.getElementById("sound").play()
  });
  reader.readAsDataURL(obj.files[0]);
}

</script>

<?php } ?>