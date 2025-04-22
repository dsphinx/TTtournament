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
 *  Filename: logViewer.php
 *  Created : 3/9/17 11:49
 */


require_once  'App/Files.php';

$tmp=miniMVController::$db->get_rows("SELECT name, path FROM logfiles", TRUE);

foreach ( $tmp as $v )
{
	Files::$logfiles[$v['name']]=$v['path'];
}
ksort(Files::$logfiles);


function showInformation($object, $title=" Info ")
{
	$ret = array();
	static $k;
	$k++;

	$div="Info" . $k;
	$ret[0]=' <div class="btn-group"><button data-toggle="button" type="button" onclick="javascript:toggleDiv(\'' . $div . '\',\'' . $realfile . '\');" class="btn btn-info btn-xs">' . $title . '</button>&nbsp;</div>';
	$ret[1]= "<div id=\"$div\" style=\"display: none;\"><hr>   <br> ";
	$ret[1].= ($object);
	$ret[1].= "</div>";

	return $ret;
}


echo '
<script type="text/javascript">
    function toggleDiv(divId) {
            $("#"+divId).toggle();
    }
</script>
    ';


$all = miniMVController::param('all');

$menu = NULL;
$logs =NULL;

foreach ( Files::$logfiles as $name=>$val ) {




		$text=Files::tailReturn($val, 3128, FALSE);

		if ( $all )
		{
			echo $text;
		}
		else
		{
			if (!is_null($text))
			{
				$ret =showInformation($text, $name);
				$menu.=$ret[0];
				$logs.=$ret[1];
			}
		}


}



echo '
<div id="menusToggle" class="table-responsive btn-group btn-group-justified"> ' . $menu . '</div>
<div id="logsAre">'.$logs.'</div>
';