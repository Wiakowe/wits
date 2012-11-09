<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Wiakowe\FetchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class MailProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('wiakowe_fetch.processing.planner')) {
            return;
        }

        $planner = $container->getDefinition('wiakowe_fetch.processing.planner');
        foreach ($container->findTaggedServiceIds('wiakowe_fetch.processor') as $id => $attr) {
            $options = $this->getOptionResolver()->resolve($attr[0]);

            $planner->addMethodCall('addProcessor', array(new Reference($id), $options['priority']));
        }
    }

    /**
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected function getOptionResolver()
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(
            array(
                'priority' => 0
            )
        );

        return $resolver;
    }
}
