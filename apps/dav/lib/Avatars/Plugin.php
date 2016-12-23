<?php
/**
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2016, ownCloud GmbH
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

namespace OCA\DAV\Avatars;

use Sabre\DAV\INode;
use Sabre\DAV\PropFind;
use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;
use Sabre\DAV\Xml\Property\LocalHref;
use Sabre\DAVACL\IPrincipal;

class Plugin extends ServerPlugin {

	const AVATAR_ROOT = 'avatars';

	/** @var Server */
	protected $server;

	/**
	 * Initializes the plugin and registers event handlers
	 *
	 * @param Server $server
	 * @return void
	 */
	function initialize(Server $server) {

		$this->server = $server;
		$server->on('propFind', [$this, 'propFind'], 20);
	}

	function propFind(PropFind $propFind, INode $node) {

		/* Adding principal properties */
		if ($node instanceof IPrincipal) {
			$principalUrl = $node->getPrincipalUrl();

			$propFind->handle('{http://owncloud.org/ns}avatar-home', function() use ($principalUrl) {

				$avatarHome = $this->getAvatarHome($principalUrl);
				if (is_null($avatarHome)) {
					return null;
				}
				return new LocalHref($avatarHome);

			});
		}
	}

	private function getAvatarHome($principalUrl) {
		if (strrpos($principalUrl, 'principals/users', -strlen($principalUrl)) !== false) {
			$user = substr($principalUrl, strlen('principals/users/'));
			return "avatars/$user";
		}

		return false;
	}

}
