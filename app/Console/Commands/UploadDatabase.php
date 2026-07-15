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
        //
    }
}
