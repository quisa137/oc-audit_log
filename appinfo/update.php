<?php
$installedVersion = OCP\Config::getAppValue ( 'audit_log', 'installed_version' );
// fetch from DB
$query = \OC_DB::prepare ( 'SELECT ' .
  ' FROM `*PREFIX*preferences` ' . ' WHERE `appid` = ? AND `configkey` = ? ' );
$result = $query->execute ( array (
 'activity','notify_stream'
) );