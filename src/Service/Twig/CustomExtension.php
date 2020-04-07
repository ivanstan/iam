<?php

namespace App\Service\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CustomExtension extends AbstractExtension
{
    protected string $env;
    protected RequestStack $request;
    protected UrlGeneratorInterface $generator;

    public function __construct($env, RequestStack $request, UrlGeneratorInterface $generator)
    {
        $this->env = $env;
        $this->request = $request;
        $this->generator = $generator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('test', [$this, 'testAttribute']),
            new TwigFunction('sort_link', [$this, 'sortLink']),
        ];
    }

    public function testAttribute(string $attribute): string
    {
        if ($this->env === 'prod') {
            return '';
        }

        return "data-test=$attribute";
    }

    public function sortLink(string $name, string $title): string
    {
        $request = $this->request->getMasterRequest();

        $query = $request->query->all();

        if (!isset($query['sort'])) {
            $query['sort'] = $name;
            $query['sort-dir'] = 'asc';
            $icon = '<i class="fas fa-sort"></i>';
        } elseif ($query['sort'] === $name) {
            if ($query['sort-dir'] === 'asc') {
                $query['sort-dir'] = 'desc';
                $icon = '<i class="fas fa-sort-up"></i>';
            } else {
                $query['sort-dir'] = 'asc';
                $icon = '<i class="fas fa-sort-down"></i>';
            }
        } else {
            $query['sort'] = $name;
            $query['sort-dir'] = 'asc';
            $icon = '<i class="fas fa-sort"></i>';
        }

        $href = $this->generator->generate($request->get('_route'), $query);

        return "<a href='$href'>$title</a><span class='pl-1 text-primary'>$icon</span>";
    }
}
