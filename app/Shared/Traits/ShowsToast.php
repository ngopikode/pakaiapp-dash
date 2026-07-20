<?php

namespace App\Shared\Traits;

trait ShowsToast
{
    protected function toast(string $message, string $type = 'success'): void
    {
        $messageJson = json_encode($message, JSON_THROW_ON_ERROR);
        $this->js("window.showIslandToast($messageJson, '$type');");
    }
}
