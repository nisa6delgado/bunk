<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('upload-database')]
#[Description('Upload database to the cloud')]
class UploadDatabase extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = database_path() . '/database.sqlite';
        $fp = fopen($file, 'r');

        try {
            $ftp_connect = ftp_connect(env('FTP_SERVER'));
            $ftp_login = ftp_login($ftp, env('FTP_USER'), env('FTP_PASSWORD'));
            ftp_put($ftp_connect, 'database/database.sqlite', $file, FTP_ASCII);

            $this->info('Base de datos cargada exitosamente');
        }
        catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
