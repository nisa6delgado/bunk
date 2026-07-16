<?php

namespace App\Console\Commands;

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

        if (env('FTP_SERVER')) {
            $ftp = ftp_connect(env('FTP_SERVER'));
        }

        if (env('FTP_USER') && env('FTP_PASSWORD')) {
            $login = ftp_login($ftp, env('FTP_USER'), env('FTP_PASSWORD'));
        }
    }
}
