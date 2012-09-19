<?php

namespace Wiakowe\FetchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wiakowe\FetchBundle\DependencyInjection\Compiler\MailProcessorPass;
use Wiakowe\FetchBundle\DependencyInjection\Compiler\MailFilterPass;

class WiakoweFetchBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MailProcessorPass());
        $container->addCompilerPass(new MailFilterPass());
    }
}
