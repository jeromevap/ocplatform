<?php


namespace App\Antispam;


class Antispam
{
    /**
     * Vérifie si le texte est un spam ou non
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text): bool
    {
        return strlen($text) < 50;
    }
}