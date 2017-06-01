<?php

namespace Appstract\RefererRedirector\Console;

use Illuminate\Console\Command;
use Appstract\RefererRedirector\RefererRedirect;

class ListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referer:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all referer redirectors';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->table(
            ['ID', 'Referer url', 'Redirect url', 'Start date', 'End date'],
            RefererRedirect::all()->toArray()
        );
    }
}
