<?php

namespace Snappminds\Utils\Bundle\FormBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SnappmindsUtilsFormExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);                        

        /* Se agregan los templates definidos para los widgets nuevos */
        $resources = $container->getParameter('twig.form.resources');
        
        $resources = array_merge($resources, array(
            'SnappmindsUtilsFormBundle:Form:choice.html.twig',
            'SnappmindsUtilsFormBundle:Form:date.html.twig',
            'SnappmindsUtilsFormBundle:Form:predicttext.html.twig',
            'SnappmindsUtilsFormBundle:Form:entitycontainer.html.twig'
        ));     
                
        $container->setParameter('twig.form.resources', $resources);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
    }
}
