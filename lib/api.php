<?php

/**
 * ownCloud - Audit_log App
 *
 * @author Joas Schilling
 * @copyright 2014 Joas Schilling nickvergessen@owncloud.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Audit_log;

/**
 * Class Api
 *
 * @package OCA\Audit_log
 */
class Api
{
	const DEFAULT_LIMIT = 30;

	static public function get($param) {
		$l = \OCP\Util::getL10N('audit_log');
		$data = new \OCA\Audit_log\Data(\OC::$server->getAudit_logManager());
		$groupHelper = new \OCA\Audit_log\GroupHelper(
			\OC::$server->getAudit_logManager(),
			new \OCA\Audit_log\DataHelper(
				\OC::$server->getAudit_logManager(),
				new \OCA\Audit_log\ParameterHelper(new \OC\Files\View(''), $l),
				$l
			),
			false
		);

		$start = isset($_GET['start']) ? $_GET['start'] : 0;
		$count = isset($_GET['count']) ? $_GET['count'] : self::DEFAULT_LIMIT;

		$activities = $data->read($groupHelper, $start, $count, 'all');
		$data = array();
		foreach($activities as $entry) {
			$data[] = array(
				'id' => $entry['log_id'],
				'subject' => (string) $entry['subjectformatted']['full'],
				'file' => $entry['file'],
				'link' => $entry['link'],
				'date' => date('c', $entry['timestamp']),
			);
		}

		return new \OC_OCS_Result($data);
	}
}
