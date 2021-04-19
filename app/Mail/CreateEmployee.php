<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreateEmployee extends Mailable
{
    use Queueable, SerializesModels;

    private string $email = '';
    private string $password = '';
    private string $role = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $email, string $password, string $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_USERNAME'))->view('mails.createEmployee', [
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role
        ])->subject('Новый аккаунт DiamondsMatch');
    }
}
