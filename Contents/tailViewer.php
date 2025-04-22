 <?php
/**
 *  Copyright (c) 2017, dsphinx
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 *   1. Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *   2. Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *   3. All advertising materials mentioning features or use of this software
 *      must display the following acknowledgement:
 *      This product includes software developed by the dsphinx.
 *   4. Neither the name of the dsphinx nor the
 *      names of its contributors may be used to endorse or promote products
 *     derived from this software without specific prior written permission.
 *
 *  THIS SOFTWARE IS PROVIDED BY dsphinx ''AS IS'' AND ANY
 *  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL dsphinx BE LIABLE FOR ANY
 *  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *  Filename: tailViewer.php
 *  Created : 17/9/17 09:56
 */


require_once 'App/Files.php';

$tmp=miniMVController::$db->get_rows("SELECT name, path FROM logfiles", TRUE);

foreach ( $tmp as $v )
{
	Files::$logfiles[$v['name']]=$v['path'];
}
ksort(Files::$logfiles);

function showInformation($object, $title=" Info ", $realfile)
{
	$ret=array();
	static $k;
	$k++;

	$div   ="Info" . $k;
	$ret[0]=' <div class="btn-group"><button title="'.$k." $title".'" id="log'.$k.'" data-toggle="button" type="button" onclick="toggleDiv(\'' . $div . '\',\'' . $realfile . '\');" class="btn btn-info btn-xs">' . $title . '</button>&nbsp;</div>';

	return $ret;
}


echo '
<script type="text/javascript">
    function toggleDiv(divId, realfile) {   
        
        var info ="";
        
          tailFileIS = realfile;
          $.getJSON(scriptIS + "?log=" + tailFileIS, function (data) {     
            offset = data["offset"]; info = data["size"];  
            $("#tailOutput").append("<hr/><span class=\'pull-right\'><mark>*** File : "+ realfile + " </mark>,  size = <b>"+ info +"</b> bytes </span><br/>");
            show.log(\' file :  \'+ realfile);

          });                 

          $("#"+divId).toggle();
    }
    
    
</script>


    ';


$all=miniMVController::param('all');

$menu=NULL;
$logs=NULL;

foreach ( Files::$logfiles as $name=>$val )
{

	$text=Files::tail($val, 3128, FALSE);

	if ( $all )
	{
		echo $text;
	}
	else
	{

		if ($text == 1)
		{
			$ret =showInformation($text, $name, $val);
			$menu.=$ret[0];
		}
	}

}


echo '
<script>
  var offset, tailFileIS = "/var/log/messages";
</script>
<script src="App/Javascript/FilesTail.js"></script>

<div id="menusToggle" class="table-responsive btn-group btn-group-justified"> ' . $menu . ' </div>

<div class="tools">
	<label class="switch">on
	  <input id="scroll" type="checkbox">
	  <span title="auto scrolling " class="slider round"></span>
	</label> 
	<button title="copy all to clipboard" style="margin-top: -2px; margin-right: 2px; padding: 2px 2px 2px 2px" class="btn btn-xs btn-warning" onclick="copyToClipboard(\'#tailClipboard\')"> &#x2398; </button>  
</div>
<div id="logsAre"><div id="tailOutput"></div>
	<div id="tailClipboard" style="visibility: hidden; display: none;"></div>
 </div>
';

miniMVController::show("loading default logs fail -- messages");
