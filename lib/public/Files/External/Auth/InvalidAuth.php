<?php
/**
 * @author Vincent Petry <pvince81@owncloud.com>
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

namespace OCP\Files\External\Auth;

/**
 * Invalid authentication representing an auth mechanism
 * that could not be resolved
 */
class InvalidAuth extends AuthMechanism {

	public function __construct(IL10N $l, $invalidId) {
		$this
			->setIdentifier($invalidId)
			->setScheme(self::SCHEME_NULL)
			->setText('Unknown auth mechanism backend ' . $invalidId)
		;
	}

}
