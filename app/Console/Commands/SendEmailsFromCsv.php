<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\MailerLiteHtmlMail;
use Illuminate\Support\Facades\Http;

class SendEmailsFromCsv extends Command
{
    protected $signature = 'emails:send {csvPath}';
    protected $description = 'Envoie des emails aux adresses d’un fichier CSV avec contenu HTML MailerLite';

    public function handle()
    {
        $csvPath = $this->argument('csvPath');

        if (!Storage::exists($csvPath)) {
            $this->error("Fichier CSV introuvable : $csvPath");
            return;
        }

        // Récupération du contenu HTML
        $url = 'https://preview.mailerlite.io/preview/1384356/emails/151123009858438322';
        $htmlContent = Http::get($url)->body();

        $emails = array_map('str_getcsv', explode("\n", Storage::get($csvPath)));
        $emails = array_filter(array_column($emails, 0)); // première colonne = email

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new MailerLiteHtmlMail($htmlContent));
        
                // Enregistrement dans sent.txt
                file_put_contents(storage_path('app/sent.txt'), $email . PHP_EOL, FILE_APPEND);
        
                $this->info("Envoyé à : $email");
                sleep(1); // Limiter le débit pour éviter les blocages
            } catch (\Exception $e) {
                // Enregistrement dans not_sent.txt
                file_put_contents(storage_path('app/notsent.txt'), $email . ' - ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        
                $this->error("Erreur pour $email : " . $e->getMessage());
            }
        }

        $this->info('Tous les emails ont été envoyés.');
    }
}
