<?php

namespace App\Service\Generator;

use Twig\Environment;

class ReactEntityGenerator extends AbstractGenerator
{
    public const FILES = [
        [
            'class' => '{{className}}CreatePage',
            'template' => 'generator/entity/pages/create-page.tsx.twig',
            'fileName' => 'assets/pages/{{className}}/{{className}}CreatePage.tsx',
            'route' => '{{routeName}}/new',
        ],
        [
            'class' => '{{className}}EditPage',
            'template' => 'generator/entity/pages/edit-page.tsx.twig',
            'fileName' => 'assets/pages/{{className}}/{{className}}EditPage.tsx',
            'route' => '{{routeName}}/{id}/edit',
        ],
        [
            'class' => '{{className}}ListPage',
            'template' => 'generator/entity/pages/list-page.tsx.twig',
            'fileName' => 'assets/pages/{{className}}/{{className}}ListPage.tsx',
            'route' => '{{routeName}}s',
        ],
        [
            'class' => '{{className}}DetailPage',
            'template' => 'generator/entity/pages/detail-page.tsx.twig',
            'fileName' => 'assets/pages/{{className}}/{{className}}DetailPage.tsx',
            'route' => '{{routeName}}/{id}',
        ],
        [
            'class' => '{{className}}Form',
            'template' => 'generator/entity/form.tsx.twig',
            'fileName' => 'assets/forms/{{className}}Form.tsx',
        ],
    ];

    public function __construct(protected Environment $twig)
    {
    }

    public function generate(DoctrineEntityGeneratorParameter $parameter): void
    {
        foreach (self::FILES as $data) {
            $content = $this->twig->render(
                $data['template'],
                [
                    'className' => $parameter->getClassName(),
                    'routeName' => $parameter->getRouteName(),
                ]
            );

            $this->dump($this->interpolate($data['fileName'], $parameter), $content);
        }
    }

    public function interpolate(string $file, DoctrineEntityGeneratorParameter $parameter): string
    {
        $replace = [
            '{{className}}' => $parameter->getClassName(),
            '{{routeName}}' => $parameter->getRouteName(),
        ];

        return str_replace(array_keys($replace), $replace, $file);
    }
}
