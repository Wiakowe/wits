<?php
namespace Wits\HelperBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter as BaseParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class DoctrineParamConverter extends BaseParamConverter
{
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $class = $configuration->getClass();
        $options = $this->getOptions($configuration);

        // find by identifier?
        if (false === $object = $this->find($class, $request, $options, $configuration->getName())) {
            throw new \LogicException('Unable to guess how to get a Doctrine instance from the request information.');
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        $request->attributes->set($configuration->getName(), $object);

        return true;
    }

    protected function find($class, Request $request, $options, $name)
    {
        if (!($id = $this->getId($request, $options, $name))) {
            return null;
        }

        return $this->registry->getRepository($class, $options['entity_manager'])->find($id);
    }

    protected function getId(Request $request, $options, $name = null)
    {
        if (array_key_exists('parameter', $options)) {
            if ($request->attributes->has($options['parameter'])) {
                return $request->attributes->get($options['parameter']);
            }
            return false;
        }
        $attribute = $name;

        $possibleKeys = array('id', $attribute, $attribute . '_id', $attribute . 'Id');

        foreach ($possibleKeys as $possibleKey) {
            if ($request->attributes->has($possibleKey)) {
                return $request->attributes->get($possibleKey);
            }
        }

        return false;
    }
}
