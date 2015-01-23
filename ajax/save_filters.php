<?php
namespace OCA\Audit_log;

\OCP\JSON::callCheck();
\OCP\JSON::checkAdminUser();
\OCP\JSON::setContentTypeHeader();

$filters = isset($_POST['filters']) ? $_POST['filters'] : null;
$defaultEmails = isset($_POST['defaultEmails']) ? $_POST['defaultEmails'] : null;

try {
    if (!is_null($filters)){
        Settings::setFilters($filters);
    } else if(!is_null($defaultEmails)) {
        Settings::setDefaultEmails($defaultEmails);
    }
    $l = \OCP\Util::getL10N('audit_log');
    \OCP\JSON::success(array('data'=>array('message' => $l->t('Saved'),'filters' => $filters)));
} catch (\Exception $e){
    \OCP\JSON::error(array('data'=>array('message' => $e->getMessage())));
}
