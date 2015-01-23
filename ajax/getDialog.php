<?php

/**
 * SGcom
 */
\OCP\JSON::checkLoggedIn();
\OCP\JSON::checkAppEnabled('audit_log');
$l = \OCP\Util::getL10N('audit_log');
$dialog = $_POST['dialog'];

$tmpl = new \OCP\Template('audit_log', $dialog.'Dialog', '');
$tmpl->assign('defaultEmails',\OCA\Audit_log\Settings::getDefaultEmails());
$tmpl->printPage();
?>