<?php

namespace App\Controller\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ThemeExtension extends AbstractExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_theme', [$this, 'getCurrentTheme']),
        ];
    }

    public function getCurrentTheme(): string
    {
        return $this->requestStack->getCurrentRequest()->getSession()->get('theme', 'light');
    }
}

