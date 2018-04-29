<?php
/**
 * @author Samy NASTUZZI <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, Samy NASTUZZI (samy@nastuzzi.fr).
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

namespace OCA\Files_external_gdrive\Storage;

use Icewind\Streams\IteratorDirectory;
use League\Flysystem\FileNotFoundException;

abstract class Flysystem extends \OC\Files\Storage\Flysystem {
	/**
	 * {@inheritdoc}
	 */
	public function file_get_contents($path) {
		return $this->fopen($path, 'r');
	}

	/**
	 * {@inheritdoc}
	 */
	public function filesize($path) {
 		$stat = $this->stat($path);

 		return $stat['size'];
	}

	/**
	 * {@inheritdoc}
	 */
 	public function filemtime($path) {
 		$stat = $this->stat($path);

 		return $stat['mtime'];
 	}
}
