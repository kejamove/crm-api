<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class MailConfigService
{
    /**
     * Set the mail configuration dynamically.
     *
     * @param string $mailer
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $encryption
     * @param string $fromAddress
     * @param string $fromName
     * @return void
     */
    public static function setMailConfig($mailer, $host, $port, $username, $password, $encryption, $fromAddress, $fromName)
    {
        Config::set('mail.mailers.smtp.transport', $mailer);
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', $port);
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.mailers.smtp.encryption', $encryption);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);
    }
}


