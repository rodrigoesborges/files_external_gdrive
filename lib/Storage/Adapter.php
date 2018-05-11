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

class Adapter extends \Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter {
	const FOLDER = 'application/vnd.google-apps.folder';
	const DOCUMENT = 'application/vnd.google-apps.document';
	const SPREADSHEET = 'application/vnd.google-apps.spreadsheet';
	const DRAWING = 'application/vnd.google-apps.drawing';
	const PRESENTATION = 'application/vnd.google-apps.presentation';
	const MAP = 'application/vnd.google-apps.map';

    /**
     * List contents of a directory.
     *
     * @param string $path
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents($path = '', $recursive = false) {
        $contents = $this->getItems($path, $recursive);

		foreach ($contents as $key => $content) {
			$extension = isset($content['mimetype']) ? $this->getGoogleDocExtension($content['mimetype']) : '';

			$contents[$key]['basename'] = $content['filename'].($content['extension'] === '' ? '' : '.'.$content['extension']).($extension === '' ? '' : '.'.$extension);
		}

		return $contents;
    }

	/**
	* Generate file extension for a Google Doc, choosing Open Document formats for download
	* @param string $mimetype
	* @return string
	*/
	private function getGoogleDocExtension($mimetype) {
		switch ($mimetype) {
			case self::DOCUMENT:
				return 'odt';
			case self::SPREADSHEET:
				return 'ods';
			case self::DRAWING:
				return 'jpg';
			case self::PRESENTATION:
				return 'pdf';
			default:
				return '';
		}
	}
}
