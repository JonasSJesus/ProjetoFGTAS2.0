<?php

namespace Fgtas\Helpers;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class SessionTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return [
            'session' => $_SESSION ?? []
        ];
    }
}