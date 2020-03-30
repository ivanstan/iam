<?php

namespace App\Service\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorAwareTrait
{
    private TranslatorInterface $translator;

    /**
     * @required
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
