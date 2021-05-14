<?php

namespace App\Utils\Match;

trait ProcessCore
{
    /**
     * Проверяем, что этот пол нам подходит
     */
    public function isSex(): bool
    {
        return $this->currentMy['sex'] == $this->currentPartner['sex'];
    }

}
