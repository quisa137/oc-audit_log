<?php

$installedVersion = OCP\Config::getAppValue('audit_log', 'installed_version');

if (version_compare($installedVersion, '1.1.6', '<')) {
	$type_map = array(
		1 => \OCA\Audit_log\Data::TYPE_SHARE_CHANGED,
		2 => \OCA\Audit_log\Data::TYPE_SHARE_DELETED,
		3 => \OCA\Audit_log\Data::TYPE_SHARE_CREATED,
		4 => \OCA\Audit_log\Data::TYPE_SHARED,
		5 => \OCA\Audit_log\Data::TYPE_SHARED,
		6 => \OCA\Audit_log\Data::TYPE_SHARE_CHANGED,
		7 => \OCA\Audit_log\Data::TYPE_SHARE_DELETED,
		8 => \OCA\Audit_log\Data::TYPE_SHARE_CREATED,
		9 => \OCA\Audit_log\Data::TYPE_SHARE_EXPIRED,
		10 => \OCA\Audit_log\Data::TYPE_SHARE_RESHARED,
		11 => \OCA\Audit_log\Data::TYPE_SHARE_RESHARED,
		12 => \OCA\Audit_log\Data::TYPE_SHARE_DOWNLOADED,
		13 => \OCA\Audit_log\Data::TYPE_SHARE_UPLOADED,
		14 => \OCA\Audit_log\Data::TYPE_STORAGE_QUOTA_90,
		15 => \OCA\Audit_log\Data::TYPE_STORAGE_FAILURE,
		16 => \OCA\Audit_log\Data::TYPE_SHARE_UNSHARED,
	);

	foreach ($type_map as $old_type => $new_type) {
		$query = \OC_DB::prepare('UPDATE `*PREFIX*activity` SET `type` = ? WHERE `type` = ?');
		$query->execute(array($new_type, $old_type));
	}

	// fetch from DB
	$query = \OC_DB::prepare(
		'SELECT `userid`, `configvalue` '
		. ' FROM `*PREFIX*preferences` '
		. ' WHERE `appid` = ? AND `configkey` = ? '
	);
	$result = $query->execute(array('audit_log', 'notify_stream'));

	$preference_map = array(
		1 => \OCA\Audit_log\Data::TYPE_SHARE_CHANGED,
		2 => \OCA\Audit_log\Data::TYPE_SHARE_DELETED,
		3 => \OCA\Audit_log\Data::TYPE_SHARE_CREATED,
		4 => \OCA\Audit_log\Data::TYPE_SHARED,
		9 => \OCA\Audit_log\Data::TYPE_SHARE_EXPIRED,
		10 => \OCA\Audit_log\Data::TYPE_SHARE_RESHARED,
		12 => \OCA\Audit_log\Data::TYPE_SHARE_DOWNLOADED,
		13 => \OCA\Audit_log\Data::TYPE_SHARE_UPLOADED,
		14 => \OCA\Audit_log\Data::TYPE_STORAGE_QUOTA_90,
		15 => \OCA\Audit_log\Data::TYPE_STORAGE_FAILURE,
		16 => \OCA\Audit_log\Data::TYPE_SHARE_UNSHARED,
	);

	$query = \OC_DB::prepare('INSERT INTO `*PREFIX*preferences` (`userid`, `appid`, `configkey`, `configvalue`)' . ' VALUES ( ?, ?, ?, ? )');
	while ($row = $result->fetchRow()) {
		foreach ($preference_map as $old_type => $new_type) {
			$query->execute(array(
				$row['userid'],
				'audit_log',
				'notify_stream_' . $new_type,
				in_array($old_type, unserialize($row['configvalue'])),
			));
		}
	}

	$query = \OC_DB::prepare('DELETE FROM `*PREFIX*preferences` WHERE `appid` = ? AND (`configkey` = ? OR `configkey` = ?)');
	$query->execute(array('audit_log', 'notify_stream', 'notify_email'));
}

if (version_compare($installedVersion, '1.1.11', '<')) {
	$query = \OC_DB::prepare(
		'DELETE FROM `*PREFIX*activity`'
		. ' WHERE `subject` = ?'
	);

	$query->execute(array('%s shared %s with you'));
}

if (version_compare($installedVersion, '1.1.10', '<')) {
	$subject_map = array(
		'%s created'		=> 'created_self',
		'%s created by %s'	=> 'created_by',
		'%s changed'		=> 'changed_self',
		'%s changed by %s'	=> 'changed_by',
		'%s deleted'		=> 'deleted_self',
		'%s deleted by %s'	=> 'deleted_by',
		'You shared %s with %s'			=> 'shared_user_self',
		'You shared %s with group %s'	=> 'shared_group_self',
		'%s shared %s with you'			=> 'shared_with_by',
		'You shared %s'					=> 'shared_link_self',
	);

	foreach ($subject_map as $old_subject => $new_subject) {
		$query = \OC_DB::prepare('UPDATE `*PREFIX*activity` SET `subject` = ? WHERE `subject` = ?');
		$query->execute(array($new_subject, $old_subject));
	}
}