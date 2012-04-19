<?php require_once '../inc/settings.inc.php'; ?>
<?php

$api = new Pingdom_API(PINGDOM_USR, PINGDOM_PWD);
try {
	$resp = $api->getChecks();
	echo json_encode($resp);
} catch (Exception $e) {
	echo "{ error: \"" . $e->getMessage() . "}";
}

?>
