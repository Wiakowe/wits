<?php
namespace Wits\HelperBundle\Twig\Extension;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Aitor Suso patxi1980@gmail.com
 */
class ProjectIdExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getGlobals()
    {
        $projectId = null;

        if ($this->container->isScopeActive('request') && $this->container->has('request')) {
            $attributes = $this->container->get('request')->attributes;

            if ($attributes->get('project_id')) {
                $projectId = $attributes->get('project_id');
            } elseif ($attributes->get('id')) {
                $projectId = $attributes->get('id');
            }
        }

        return array('project_id' => $projectId);
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return "wits_project_id";
    }
}
