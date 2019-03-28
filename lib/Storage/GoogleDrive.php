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

use GuzzleHttp\Exception\RequestException;
use Icewind\Streams\IteratorDirectory;
use Icewind\Streams\RetryWrapper;
use Icewind\Streams\CallbackWrapper;

class GoogleDrive extends Flysystem
{
    const APP_NAME = 'files_external_gdrive';

    protected $config = [
        'retry' => [
            'retries' => 1,
        ],
    ];
    protected $scopes = [
        \Google_Service_Drive::DRIVE,
    ];

    protected $clientId;
    protected $clientSecret;
    protected $accessToken;

    private $client;
    private $id;
    private $service;

    protected $adapter;
    protected $flysystem;

    protected $logger;

    protected $root = 'root';

    /**
     * Initialize the storage backend with a flyssytem adapter
     * @override
     * @param \League\Flysystem\Filesystem $fs
     */
    public function setFlysystem($fs)
    {
        $this->flysystem = $fs;
        $this->flysystem->addPlugin(new \League\Flysystem\Plugin\GetWithMetadata());
    }

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    public function __construct($params)
    {
        if (isset($params['configured']) && $params['configured'] === 'true'
        && isset($params['client_id']) && isset($params['client_secret'])
        && isset($params['token'])
        ) {
            $this->clientId = $params['client_id'];
            $this->clientSecret = $params['client_secret'];
            $this->accessToken = $params['token'];

            $this->client = new \Google_Client($this->config);
            $this->client->setClientId($this->clientId);
            $this->client->setClientSecret($this->clientSecret);
            $this->client->setScopes($this->scopes);
            $this->client->setAccessToken($this->accessToken);

            $this->service = new \Google_Service_Drive($this->client);
            $token = json_decode($this->accessToken, true);
            $this->id = 'google::'.substr($this->clientId, 0, 30).$token['created'];

            $this->adapter = new Adapter($this->service);
            // $this->adapter = new Adapter($this->service, [
            //     'defaultParams' => [
            //         'files.list' => [
            //             'includeTeamDriveItems' => true,
            //         ],
            //     ]
            // ]);
            // $this->adapter->enableTeamDriveSupport();

            $this->buildFlySystem($this->adapter);

            $this->logger = \OC::$server->getLogger();
        } else if (isset($params['configured']) && $params['configured'] === 'false') {
            throw new \Exception('Google storage not yet configured');
        } else {
            throw new \Exception('Creating Google storage failed');
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function free_space($path)
    {
        $about = $this->service->about->get(['fields' => 'storageQuota']);
        $storageQuota = $about->getStorageQuota();

        return ($storageQuota->getLimit() - $storageQuota->getUsage());
    }

    public function test()
    {
        if ($this->free_space('')) {
            return true;
        }

        return false;
    }
}
