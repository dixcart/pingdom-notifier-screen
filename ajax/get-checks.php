<?php require_once '../includes/settings.inc.php'; ?>
<?php require_once '../includes/extlib/Pingdom/API.php'; ?>
<?php

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$api = new Pingdom_API(PINGDOM_USR, PINGDOM_PWD);
try {
	$resp = $api->getChecks();
	echo json_encode($resp);
} catch (Exception $e) {
	echo "{ error: \"" . $e->getMessage() . "}";
}

?>
