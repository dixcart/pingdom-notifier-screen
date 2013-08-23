<!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">

	<title>Status</title>

	<script type="text/javascript" src="assets/js/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery-ui-1.9.0.custom.min.js"></script>
	<script type="text/javascript" src="assets/js/date.js"></script>

	<link rel="stylesheet" type="text/css" href="assets/css/reset-base-fonts.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/smoothness/jquery-ui-1.9.0.custom.min.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/screen.css" />

</head>
<body class="<?= $_GET['class'] ?>">

<div id="footer-bar">
	<div id="footer-bar-inner">
		<input type="checkbox" class="check" id="toggle-paused" checked /><label for="toggle-paused">Show Paused</label>
		<input type="checkbox" class="check" id="toggle-up" checked /><label for="toggle-up">Show Up</label>
	</div>
</div>

<div id="base-server" class="panel down">
    <h1></h1>
    <div class="status"></div>
</div>

<script type="text/javascript">

	$(document).ready(function(){
		//Build page

			$('#toggle-paused').button().click(function(){
				$('.paused').toggle('slow');
			});
			$('#toggle-up').button().click(function(){
				$('.up').toggle('slow');
			});


			//Run checks and set intervals
			fastChecks();
			//setTimeout(slowChecks, 5 * 1000);
			timer = setInterval(fastChecks, 60 * 1000);
			//timer2 = setInterval(slowChecks, 5 * 60 *1000);
	});

	function fastChecks() {
		$.ajax({
			url: 'ajax/get-checks.php'
		}).done(function(data){
			data = data.checks;
			for (i = 0; i < data.length; i++) {
				var el = $('#check_' + data[i]['id']);
				if (el.length ==0) {
					el = $('#base-server').clone();
					el.children('h1').html(data[i]['name']);
					el
						.attr('id', 'check_' + data[i]['id'])
						.attr('data-last-status', data[i]['status'])
						.removeClass('down')
						.addClass(data[i]['status'])
						.appendTo('body');
				} else {
				el
					.removeClass($('#check_' + data[i]['id']).attr('data-last-status'))
					.attr('data-last-status', data[i]['status'])
					.addClass(data[i]['status'])
				}
			}
		})

	}

	function dialogSSH(strCommand, strServerIP, strServerName) {
        $('<div title="' + strServerName + '"><pre class="server-response"><img src="assets/images/ajax-loader.gif" /></pre></div>').dialog({
            resizable: true,
            height: 300,
            width: 600,
            modal: false,
            open: function() {
            	var strUrl = "";
            	switch (strCommand)
            	{
            		case "time":
            			strUrl = "sync-times.php";
            			break;
            		case "update":
            			strUrl = "download-patch.php";
            			break;
            		case "disk-space":
            			strUrl = "disk-space.php";
            			break;
            	}
            	$.ajax({
            		url: 'ajax/' + strUrl,
            		data: "ip=" + strServerIP,
            		context: this
            	}).done(function(data) {
            		$(this).children('.server-response').html(data);
            	})
            },
            close: function() {
            	$(this).remove();
            }
        });		
	}

	function confirmDialog(strMessage, onOK) {
        $('<div title="Confirm"><p>' + strMessage + '</p></div>').dialog({
            resizable: false,
            modal: true,
            buttons: {
                "OK": function() {
                	$(this).dialog("close").remove();
                	onOK();
                },
                Cancel: function() {
                    $(this).dialog("close").remove();
                }
            }
        });		
	}

	function slowChecks() {
		times = new Array();
		versions = new Array();
		$('.server, .module').each(function() {
			//Only run if server is available
			if ($(this).hasClass('up')) {
				$.ajax({
					url: 'ajax/get-server-time.php',
					data: "ip=" + $(this).attr('data-ip') + "&type=" + $(this).attr('data-type'),
					context: this
				}).done(function(data) {
					$(this).children('.server-time').html(data);
					times.push(data);
					times.sort();
					if (times[0] == times[times.length - 1]) $('#time-variance').html(times[0]);
					else $('#time-variance').html(times[0] + ' - ' + times[times.length - 1]);
				})
				$.ajax({
					url: 'ajax/get-server-version.php',
					data: "ip=" + $(this).attr('data-ip') + "&type=" + $(this).attr('data-type'),
					context: this
				}).done(function(data) {
					$(this).children('.server-version').html(data);
					if (versions.indexOf(data) == -1) versions.push(data);
					versions.sort();
					$('#versions').html(versions.join(', '));
				})
				if ($(this).hasClass('server')) {
					$.ajax({
						url: 'ajax/mount-usage.php',
						data: "ip=" + $(this).attr('data-ip'),
						context: this
					}).done(function(data) {
						$(this).children('.disk-space').children('.progress-inner').progressbar({value: parseInt(data['\/']) });
						$(this).children('.voicemail-space').children('.progress-inner').progressbar({value: parseInt(data['\/voicemail']) });
						$(this).children('.log-space').children('.progress-inner').progressbar({value: parseInt(data['\/logs']) });
					})
				}
			}
		});
	}

</script>

</body>
</html>

