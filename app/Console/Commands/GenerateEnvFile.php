<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateEnvFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate secret keys in .env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Generate 20-character random strings
        $key    = Str::random(20);
        $secret = Str::random(20);
        $app_id = mt_rand(100000, 999999);

        // Create .env file if not exists
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }

        // Read current .env content
        $envContent = file_get_contents($envPath);

        // Update or append the variables
        $envContent = $this->setOrUpdateEnvVariable($envContent, 'REVERB_APP_KEY', $key);
        $envContent = $this->setOrUpdateEnvVariable($envContent, 'REVERB_APP_SECRET', $secret);
        $envContent = $this->setOrUpdateEnvVariable($envContent, 'REVERB_APP_ID', $app_id);
        file_put_contents($envPath, $envContent);

        // Regenerate the application key
        $this->call('key:generate');

        // Output success message
        $this->info("APP_KEY, REVERB_APP_KEY and REVERB_APP_SECRET have been generated and updated in .env");
    }

    /**
     * Update existing key or append it if not found.
     */
    protected function setOrUpdateEnvVariable(string $envContent, string $key, string $value): string
    {
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$value}";

        if (preg_match($pattern, $envContent)) {
            return preg_replace($pattern, $replacement, $envContent);
        }

        return $envContent . "\n" . $replacement;
    }
}
