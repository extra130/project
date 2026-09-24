<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client;
use Google\Service\Drive;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            Storage::extend('google', function ($app, $config) {
                $client = new Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                
                $service = new Drive($client);
                
                $options = [];
                if (isset($config['teamDriveId'])) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                if (isset($config['folder'])) {
                    $options['folderId'] = $config['folder'];
                }

                $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                
                return new \Illuminate\Filesystem\FilesystemAdapter(
                    new Filesystem($adapter, $config),
                    $adapter,
                    $config
                );
            });
        } catch (\Exception $e) {
            // Ignore
        }
    }
}