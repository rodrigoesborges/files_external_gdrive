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
	protected $cacheFileObjects = [];

    protected function buildPath($path) {
		if ($path === '')
			return $this->root;

        $fullPath = \OC\Files\Filesystem::normalizePath($path);
		$dirs = explode('/', substr($fullPath, 1));

		@list($file, $ext) = explode('.', end($dirs));
		unset($dirs[count($dirs) - 1]);

		if (count($this->cacheFileObjects) === 0)
			$this->cacheFileObjects = $this->flysystem->listContents('', true);

		$contents = $this->cacheFileObjects;
		$path = '';
		$nbrSub = 0;

		foreach ($dirs as $dir) {
			$initNbr = $nbrSub;
			foreach ($contents as $key => $content) {
				if ($content['type'] !== 'dir')
					continue;

				if ($content['dirname'] === $path) {
					if ($content['filename'] === $dir) {
						$path = $content['path'];

						$nbrSub++;
						break;
					}

					unset($contents[$key]);
				}
				elseif (substr_count($content['dirname'], '/') <= $nbrSub)
					unset($contents[$key]);
			}

			if ($initNbr === $nbrSub)
				throw new FileNotFoundException(implode('/', array_slice($paths, 0, $key)));
		}

		// We now try to find the file
		foreach ($contents as $content) {
			if ($content['dirname'] === $path) {
				if ($content['filename'] === $file) {
					if ($ext && $content['extension'] !== $ext)
						continue;

					return $content['path'];
				}
			}
		}

		throw new FileNotFoundException($fullPath);
	}
}
