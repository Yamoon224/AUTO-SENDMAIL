<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailerLiteHtmlMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $htmlContent;

    public function __construct(string $htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    public function build()
    {
        return $this->subject('Adinkra Fellowship 2026')
            ->html($this->htmlContent);
    }
}
