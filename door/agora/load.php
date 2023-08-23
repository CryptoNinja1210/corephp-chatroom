<?php
include("RtcTokenBuilder.php");
function agora_token($channel_name='', $is_host = false) {
	if(strlen($channel_name) > 0) {
		$appID = "46257813b17c46629ca0e06110c84c3a";
		$appCertificate = "cdc303a6d66b47d8bc67dbc51810904f";
		
		$role = $is_host ? RtcTokenBuilder::RolePublisher : RtcTokenBuilder::RoleSubscriber;
		$expireTimeInSeconds = 3600;
		$currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
		$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

		$token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channel_name, 0, $role, $privilegeExpiredTs);

		$respArr = [
			'status' => true,
			'token' => $token,
			'appid' => $appID
		];
	} else {
		$respArr = [
			'status' => false,
			'message' => 'Invalid Channel'
		];
	}

	return $respArr;
}

?>
