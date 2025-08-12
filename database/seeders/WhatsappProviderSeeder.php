<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsappProvider;
use App\Models\WhatsappProviderConfig;

class WhatsappProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Interakt',
                'slug' => 'interakt',
                'api_endpoint' => 'https://api.interakt.ai/v1/public/',
                'is_active' => true,
                'priority' => 1,
                'rate_limit_per_minute' => 100,
                'configs' => [
                    ['config_key' => 'api_key', 'config_value' => '', 'is_encrypted' => false],
                ]
            ],
            [
                'name' => 'Gupshup',
                'slug' => 'gupshup',
                'api_endpoint' => 'https://api.gupshup.io/sm/api/v1/',
                'is_active' => true,
                'priority' => 2,
                'rate_limit_per_minute' => 60,
                'configs' => [
                    ['config_key' => 'api_key', 'config_value' => '', 'is_encrypted' => false],
                    ['config_key' => 'app_name', 'config_value' => '', 'is_encrypted' => false],
                ]
            ],
            [
                'name' => 'Gallabox',
                'slug' => 'gallabox',
                'api_endpoint' => 'https://api.gallabox.com/v1/',
                'is_active' => true,
                'priority' => 3,
                'rate_limit_per_minute' => 80,
                'configs' => [
                    ['config_key' => 'api_key', 'config_value' => '', 'is_encrypted' => false],
                    ['config_key' => 'workspace_id', 'config_value' => '', 'is_encrypted' => false],
                ]
            ],
        ];

        foreach ($providers as $providerData) {
            $configs = $providerData['configs'];
            unset($providerData['configs']);
            
            $provider = WhatsappProvider::updateOrCreate(
                ['slug' => $providerData['slug']],
                $providerData
            );
            
            foreach ($configs as $config) {
                $config['provider_id'] = $provider->id;
                WhatsappProviderConfig::updateOrCreate(
                    [
                        'provider_id' => $provider->id,
                        'config_key' => $config['config_key']
                    ],
                    $config
                );
            }
        }
    }
}