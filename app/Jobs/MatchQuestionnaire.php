<?php

namespace App\Jobs;

use App\Models\Questionnaire;
use App\Services\MatchProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MatchQuestionnaire implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public int $uniqueFor = 3600;
    protected Questionnaire $questionnaires;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Questionnaire $questionnaires)
    {
        $this->questionnaires = $questionnaires;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MatchProcessor $processor)
    {
        $processor->start($this->questionnaires);
    }
}
