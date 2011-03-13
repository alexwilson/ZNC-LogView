<?php

	$chdir = array('..', '/', '~', '#',);
	$denied = array('gbatemp.eof', 'bearcave', 'ndscheats-staff', '*');
	$denymsg = array('Access Denied');
	$logpath = ('/home/antoligy/.znc/users/antoligy/moddata/log/');
	$scheme = array('background' => '#FFFFFF', 'foreground' => '#000', 'link' => '#00F');

	if(isset($_GET['chan'])) {
		foreach($denied as $denied) {
			if($_GET['chan'] == $denied) {
				die('Access Denied');
			}
			else {
				$chan = str_replace($chdir, '', $_GET['chan']);
			}
		}
	}
	else {
		$chan = 'gbatemp.net';
	}
	
	$remove = array('.log', $logpath . '#' . $chan . '_');
	
	if(isset($_GET['date'])) {
		$logfile = ($logpath . '#' . $chan . '_' . str_replace($chdir, '', $_GET['date']) . '.log');
		$fh = @fopen($logfile, 'r');
		$logdata = @fread($fh, filesize($logfile));
		@fclose($fh);
		if(isset($_GET['raw'])) {
			header('Content-type: text/plain');
			die($logdata);
		}	
		else {
			$search_http = "/(http[s]*:\/\/[\S]+)/";
			$replace_http = "<a href='\${1}'>\${1}</a>";
			$html_lines = array("\r", "\n");
			$logdata = htmlspecialchars($logdata);
			$logdata = preg_replace($search_http, $replace_http, $logdata); 
			$logdata = str_replace($html_lines, '<br />' . "\r\n", $logdata);
			$begindoc = '<html> <head> <title>#' . $chan . ' logs for ' . date("F d Y", filemtime($logfile)) . '</title> </head> <body link="' . $scheme['link'] . '"alink="' . $scheme['link'] . '" vlink="' . $scheme['link'] . '" bgcolor="' . $scheme['background'] . '" text="' . $scheme['foreground'] . '"> <p> <a href="?date=' . str_replace($remove, '', $logfile) . '&chan=' . $chan . '&raw"> Raw text file </a></p><p>';
			$enddoc = '</p> </body> </html>';
			die($begindoc . $logdata . $enddoc);
		}
	} 
	else {
		$logs = glob('' . $logpath . '#' . $chan . '_*.log');
		sort($logs);
		print('<html>
		<head>
		<title>#' . $chan . ' Logs</title>
		</head>
		<body link="' . $scheme['link'] . '"alink="' . $scheme['link'] . '" vlink="' . $scheme['link'] . '" bgcolor="' . $scheme['background'] . '" text="' . $scheme['foreground'] . '">
		<p><h1><center><u>#' . $chan . ' logs:</u></center></h1></p>');
			print('<style type="text/css"> @import url(http://www.google.com/cse/api/branding.css); </style> <div class="cse-branding-bottom" style="background-color:#' . $scheme['background'] . ';color:' . $scheme['foreground'] . ';>
  					 <div class="cse-branding-form">
    				 <form action="http://www.google.co.uk/cse" id="cse-search-box">
      			 <div >
      			 <input type="hidden" name="cx" value="partner-pub-5675989731160327:eeu1h0-f703" />
      			 <input type="hidden" name="ie" value="ISO-8859-1" />
      			 <input type="text" name="q" size="31" style="width: 90%;" />
      			 <input type="submit" name="sa" value="Search" style="width: 5%;" />
      			 </div>
      			 </form>
      			 </div> 
      			 </div>');

		foreach ($logs as $filename) {
	  	print('<li><a href="?date=' . str_replace($remove, '', $filename) . '&chan=' . $chan . '">' . date("F d Y", filemtime($filename)) . '</a></li>');
		}
		print('<a href="http://phobos.stormbit.net:8033/' . $chan . '/top/total/lines/"><tt>#' . $chan . ' Stats</tt></a><br />');
		if(isset($_GET['search'])) {
			print('Search: <div id="cse" style="width: 100%;">Loading</div>
			<script src="http://www.google.com/jsapi" type="text/javascript"></script>
			<script type="text/javascript">
  			google.load("search", "1", {language : "en"});
  			google.setOnLoadCallback(function() {
    			var customSearchControl = new google.search.CustomSearchControl("009292200434156746820:j13zftk4mrm");
    			customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
    			customSearchControl.draw("cse");
  			}, true);
			</script><br />');
			};
		print('</body></html>');
	}
?>
