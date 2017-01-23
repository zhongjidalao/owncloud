<?php
/**
 * @author Victor Dubiniuk <dubiniuk@owncloud.com>
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

namespace OCA\dav\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170116170538 extends AbstractMigration {
	/**
	 * @param Schema $schema
	 */
	public function up(Schema $schema) {
		$prefix = $this->connection->getPrefix();

		// install
		if (!$schema->hasTable("${prefix}properties")) {
			$table = $schema->createTable("${prefix}properties");
			$table->addOption('collate', 'utf8_bin'); // TODO: remove once https://github.com/owncloud/core/pull/26923/ is merged
			$table->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('file_id', 'bigint', [
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('propertyname', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->addColumn('propertyvalue', 'string', [
				'notnull' => true,
				'length' => 255,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['file_id'], 'fileid_index');
		} else {
			$table = $schema->getTable("${prefix}properties");
			if (!$table->getColumn('file_id')){
				$table->addColumn('file_id', 'bigint', [
					'notnull' => true,
					'length' => 20,
				]);
				$table->addIndex(['file_id'], 'fileid_index');
			}
			if ($table->hasIndex('property_index')){
				$table->dropIndex('property_index');
			}
		}
	}

	/**
	 * @param Schema $schema
	 */
	public function down(Schema $schema)
	{
		// We can't migrate below 10.0
	}
}
