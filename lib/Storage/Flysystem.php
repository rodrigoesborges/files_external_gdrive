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

use League\Flysystem\FileNotFoundException;

abstract class Flysystem extends \OC\Files\Storage\Flysystem {
	protected $cacheFileObjects = [];

	protected function getContents($force = false) {
		if (count($this->cacheFileObjects) === 0 || $force)
			$this->cacheFileObjects = $this->flysystem->listContents($this->root, true);

		return $this->cacheFileObjects;
	}

    protected function buildPath($originalPath, $reloadContents = false) {
		if ($originalPath === '' || $originalPath === '.')
			return $this->root;

        $fullPath = \OC\Files\Filesystem::normalizePath($originalPath);

		if ($fullPath === '')
			return $this->root;

		$dirs = explode('/', substr($fullPath, 1));

		$file = end($dirs);
		unset($dirs[count($dirs) - 1]);

		$contents = $this->getContents($reloadContents);
		$path = 'root';
		$nbrSub = 1;

		foreach ($dirs as $dir) {
			$initNbr = $nbrSub;

			foreach ($contents as $key => $content) {
				if ($content['type'] !== 'dir')
					continue;

				if ($content['dirname'] === $path) {
					if ($content['basename'] === $dir) {
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
				throw new FileNotFoundException(implode('/', array_slice($dirs, 0, $key)));
		}

		// We now try to find the file
		foreach ($contents as $content) {
			if ($content['dirname'] === $path) {
				if ($content['basename'] === $file) {
					if ($ext && $content['extension'] !== $ext)
						continue;

					return $content['path'];
				}
			}
		}

		if (!$reloadContents)
			return $this->buildPath($originalPath, true);

		return $path.'/'.$file;
	}

	/**
	 * {@inheritdoc}
	 */
	public function unlink($path) {
		if ($this->is_dir($path))
			return $this->rmdir($path);
		try {
			if ($this->flysystem->delete($this->buildPath($path))) {
				$this->getContents(true);

				return true;
			}

			return false;
		} catch (FileNotFoundException $e) {
			return false;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function rmdir($path) {
		try {
			if (@$this->flysystem->deleteDir($this->buildPath($path))) {
				$this->getContents(true);

				return true;
			}

			return false;
		} catch (FileNotFoundException $e) {
			return false;
		}
	}
	/**
	 * check if a file or folder has been updated since $time
	 *
	 * The method is only used to check if the cache needs to be updated. Storage backends that don't support checking
	 * the mtime should always return false here. As a result storage implementations that always return false expect
	 * exclusive access to the backend and will not pick up files that have been added in a way that circumvents
	 * ownClouds filesystem.
	 *
	 * @param string $path
	 * @param int $time
	 * @return bool
	 */
	public function hasUpdated($path, $time) {
		// TODO
		return true;
		return $this->filemtime($path) > $time;
	}
}
