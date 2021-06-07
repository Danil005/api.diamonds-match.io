<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPrice extends Mailable
{
    use Queueable, SerializesModels;

    private string $name = '';
    private string $lang = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $name, string $lang = 'ru')
    {
        $this->name = $name;
        $this->lang = $lang;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_USERNAME'))->view('mails.sendPrice', [
            'name' => $this->name,
            'lang' => $this->lang
        ])->subject('Наши тарифы');
    }
}
