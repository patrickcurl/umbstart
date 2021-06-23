<?php

declare(strict_types=1);
namespace App\Services\Contracts;

interface ImapConnection
{
    public function checkConnection();

    public function getImapClient();
}
