<?php

namespace Appstract\RefererRedirector\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Appstract\RefererRedirector\RefererRedirect;

class MakeCommand extends Command
{
    /**
     * Referer.
     * @var string
     */
    protected $referer;

    /**
     * Redirect.
     * @var string
     */
    protected $redirect;

    /**
     * Start date.
     * @var string|object
     */
    protected $start;

    /**
     * End date.
     * @var string|object
     */
    protected $end;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referer:make
                            {referer : referer url}
                            {redirect : redirect url}
                            {--start=}
                            {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make or overwrite a referer redirector based on referer url and redirect url';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->referer = $this->argument('referer');
        $this->redirect = $this->argument('redirect');

        $this->start = $this->option('start') ? new Carbon($this->option('start')) : Carbon::now();
        $this->end = $this->option('end') ? new Carbon($this->option('end')) : null;

        // Ask for dates
        if (! $this->option('start') && ! $this->option('end')) {
            $this->promptForDates();
        }

        // Dates not valid
        if ($this->end && ! $this->validateDates()) {
            return $this->error('End date cannot be before start date, try again');
        }

        // Check for conflicts
        if ($this->hasConflictingReferers()) {
            $this->info('This referer has '.$this->getConflictingReferers()->count().' conflicting referers');

            // @TODO: Show conflicting referers

            if ($this->confirm('Do you wish to delete all conflicting referers?')) {
                $this->removeConflictedReferers();

                return $this->makeRefererRedirector();
            }
        } else {
            return $this->makeRefererRedirector();
        }
    }

    /**
     * Prompt for dates.
     *
     * @return void
     */
    protected function promptForDates()
    {
        if ($this->confirm('Do you wish to set a start date and end date?')) {
            if (! $this->option('start') && $input = $this->ask('When to start?', 'now')) {
                $this->start = new Carbon($input);
            }

            if (! $this->option('end') && $input = $this->ask('When to end?', false)) {
                $this->end = new Carbon($input);
            }
        }
    }

    /**
     * Validate date.
     *
     * @return bool
     */
    protected function validateDates()
    {
        return ! $this->end->lt($this->start);
    }

    /**
     * Get conflicting referers.
     *
     * @return \Illuminate\Support\Collection|null
     */
    protected function getConflictingReferers()
    {
        return RefererRedirect::where('referer_url', $this->referer)->get()->filter(function ($item, $key) {
            return
                // Check if match is between new dates
                $this->end && $item->start_date->between($this->start, $this->end) ||

                // Check if new dates is between match
                $item->end_date && $this->start->between($item->start_date, $item->end_date) ||

                // Match has no end date and new start date is after match' start date
                is_null($item->end_date) && $this->start->gt($item->start_date);
        });
    }

    /**
     * Check for conflicting referers.
     *
     * @return bool
     */
    protected function hasConflictingReferers()
    {
        return ! $this->getConflictingReferers()->isEmpty();
    }

    /**
     * Remove conflicting referers.
     *
     * @return void
     */
    protected function removeConflictedReferers()
    {
        $this->getConflictingReferers()->each->delete();
    }

    /**
     * Make referer redirect.
     *
     * @return string
     */
    protected function makeRefererRedirector()
    {
        $result = RefererRedirect::create([
            'referer_url' => $this->referer,
            'redirect_url' => $this->redirect,
            'start_date' => $this->start,
            'end_date' => $this->end,
        ]);

        $results[] = ['id' => $result->id] + $result->toArray();

        $this->table(
            ['ID', 'Referer url', 'Redirect url', 'Start date', 'End date'],
            $results
        );

        return $this->info('Added successfully!');
    }
}
