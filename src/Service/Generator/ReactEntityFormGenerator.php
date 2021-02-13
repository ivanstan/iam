<?php

namespace App\Service\Generator;

use Twig\Environment;

class ReactEntityFormGenerator extends AbstractGenerator
{
    use DoctrineEntityGeneratorTrait;

    public function __construct(protected Environment $twig)
    {
    }

    public function generate(string $fqn): void
    {
        $className = $this->getEntityClassNameFromFqn($fqn);

        $content = $this->twig->render(
            'generator/entity/form.tsx.twig',
            [
                'className' => $className,
            ]
        );

        $this->dump('assets/form/' . $className . 'EditForm.tsx', $content);
    }
}
