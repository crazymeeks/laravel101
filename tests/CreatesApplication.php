<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();
        $this->useSqlite($app);
        $this->app = $app;
        return $app;
    }



    /**
     * Set database to sqlite during unit test
     *
     * @param SmbApp\SMBApp $app
     * 
     * @return void
     */
    private function useSqlite($app)
    {
        // $db_driver = getenv('DB_DRIVER') ? getenv('DB_DRIVER') : 'sqlite_testing';
        // $app['config']->set('database.default', $db_driver);
        Artisan::call('migrate:refresh');
        Artisan::call('migrate');
        Hash::setRounds(4);
    }
}
