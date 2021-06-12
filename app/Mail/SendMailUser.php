<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailUser extends Mailable
{
  use Queueable, SerializesModels;

  public $user;

  public function __construct(User $user)
  {
    $this->user = $user;
  }
  
  /**
   * Create a new message instance.
   *
   * @return void
   */
  /**
   *
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->view('view.name');
  }
}
