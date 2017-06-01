<?php

namespace Appstract\RefererRedirector\Console;

use Illuminate\Console\Command;
use Appstract\RefererRedirector\RefererRedirect;

class RemoveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referer:remove
                            {referer? : referer url or id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a RefererRedirector based on referer url';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $referer = $this->argument('referer')
            ? $this->argument('referer')
            : $this->ask('Enter referer url you want to remove');

        $refererRedirect = intval($referer) > 0
            ? RefererRedirect::where('id', $referer)
            : RefererRedirect::where('referer_url', $referer);

        if (! $refererRedirect->count()) {
            return $this->info('No redirects found.');
        }

        if ($this->confirm('You\'re going to remove '.$refererRedirect->get()->count().' redirects, ok?')) {
            $refererRedirect->delete();

            return $this->info('Removed successfully!');
        }

        return $this->info('Didn\'t remove anything.');
    }
}
