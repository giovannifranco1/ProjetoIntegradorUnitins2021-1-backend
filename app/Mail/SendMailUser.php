<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailUser extends Mailable
{
  use Queueable, SerializesModels;

  private $user;
  private $codigo;
  public function __construct(User $user, $codigo)
  {
    $this->user = $user;
    $this->codigo = $codigo;
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
    return $this->from('giovannifrancorezende2012@gmail.com')
      ->view('email')
      ->subject('Recuperar senha')
      ->with([
        'user' => $this->user,
        'codigo' => $this->codigo,
      ]);
  }
}
