<?php
namespace Gwsn\Authentication\Models;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->view('gwsn-authentication::accounts.verifyEmail')
            ->from($this->data['address'], $this->data['name'])
            ->replyTo($this->data['address'], $this->data['name'])
            ->subject($this->data['subject'])
            ->with([ 'message' => $this->data['message'], 'data' => $this->data ]);
    }
}