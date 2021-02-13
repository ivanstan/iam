<?php

namespace App\Service\Generator;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Twig\Environment;

class ReactEntityRouteGenerator extends AbstractGenerator
{
    use DoctrineEntityGeneratorTrait;

    public const FILES = [
        '{{className}}CreatePage' => [
            'template' => 'generator/entity/pages/create-page.tsx.twig',
            'route' => '{{routeName}}/new',
        ],
        '{{className}}EditPage' => [
            'template' => 'generator/entity/pages/edit-page.tsx.twig',
            'route' => '{{routeName}}/{id}/edit',
        ],
        '{{className}}ListPage' => [
            'template' => 'generator/entity/pages/list-page.tsx.twig',
            'route' => '{{routeName}}s',
        ],
        '{{className}}DetailPage' => [
            'template' => 'generator/entity/pages/detail-page.tsx.twig',
            'route' => '{{routeName}}/{id}',
        ],
    ];

    public function __construct(protected Environment $twig)
    {
    }

    public function generate(string $fqn): void
    {
        $className = $this->getEntityClassNameFromFqn($fqn);
        $routeName = (new CamelCaseToSnakeCaseNameConverter())->normalize($className);

        foreach (self::FILES as $file => $data) {
            $content = $this->twig->render(
                $data['template'],
                [
                    'className' => $className,
                    'routeName' => $routeName,
                ]
            );

            $this->dump($this->getFile($file, $className), $content);
        }
    }

    public function getFile(string $file, string $className): string
    {
        $file = 'assets/pages/{{className}}/' . $file . '.tsx';

        return str_replace('{{className}}', $className, $file);
    }
}
