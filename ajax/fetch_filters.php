<?php
\OCP\JSON::checkLoggedIn();
\OCP\JSON::checkAppEnabled('audit_log');
\OCP\JSON::setContentTypeHeader();
$settings = new \OCA\Audit_log\Settings();
$filters = $settings->getFilters();
echo \OC_JSON::encode($filters);
?>