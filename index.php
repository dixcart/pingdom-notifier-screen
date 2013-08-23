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
			timer = setInterval(fastChecks, 60 * 1000);
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


</script>

</body>
</html>

