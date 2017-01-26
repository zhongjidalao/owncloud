<?php
/**
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2016, ownCloud GmbH.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\DB;

use OCP\AppFramework\QueryException;
use OCP\IDBConnection;
use OCP\Migration\IRepairStep;
use OCP\Migration\ISchemaMigration;
use OCP\Migration\ITransactionalStep;

class MigrationService {

	/**
	 * @param string $appName
	 * @param IDBConnection $connection
	 * @return MigrationConfiguration
	 * @throws \Exception
	 */
	public function buildConfiguration($appName, $connection) {
		return new MigrationConfiguration($appName, $connection);
	}

	/**
	 * @param MigrationConfiguration $migrationConfiguration
	 * @param bool $noMigrationException
	 */
	public function migrate($migrationConfiguration, $noMigrationException = false) {
		// read known migrations
		$toBeExecuted = $migrationConfiguration->getMigrationsToExecute();
		foreach ($toBeExecuted as $version => $class) {
			$instance = $this->createInstance($class);
			if ($instance instanceof ITransactionalStep) {
				$instance->beginTransaction();
			}
			if ($instance instanceof IRepairStep) {
				$instance->run(null);
			}
			if ($instance instanceof ISchemaMigration) {
				$connection = $migrationConfiguration->getConnection();
				$toSchema = $connection->createSchema();
				$sqls = $instance->changeSchema($toSchema, ['tablePrefix' => $connection->getPrefix()]);
				if (is_array($sqls)) {
					foreach ($sqls as $s) {
						$connection->executeQuery($s);
					}
				} else {
					$connection->migrateToSchema($toSchema);
				}
			}
			$migrationConfiguration->markAsExecuted($version);

			if ($instance instanceof ITransactionalStep) {
				$instance->commit();
			}
		}
	}

	private function createInstance($class) {
		try {
			$s = \OC::$server->query($class);
		} catch (QueryException $e) {
			if (class_exists($class)) {
				$s = new $class();
			} else {
				throw new \Exception("Migration step '$class' is unknown");
			}
		}

		return $s;
	}


}
