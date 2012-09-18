<?php

namespace Wiakowe\FetchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wiakowe\FetchBundle\DependencyInjection\Compiler\MailProcessorPass;

class WiakoweFetchBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MailProcessorPass());
    }
}
