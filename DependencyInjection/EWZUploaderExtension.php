<?php

namespace EWZ\Bundle\UploaderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EWZUploaderExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $container->setParameter('ewz_uploader.'.$key.'.'.$k, $v);
                }
            }

            $container->setParameter('ewz_uploader.'.$key, $value);
        }

        $this->registerWidget($container);
    }

    /**
     * Registers the form widget.
     */
    protected function registerWidget(ContainerBuilder $container)
    {
        $templatingEngines = $container->getParameter('templating.engines');

        if (in_array('php', $templatingEngines)) {
            $formRessource = 'EWZUploaderBundle:Form';

            $container->setParameter('templating.helper.form.resources', array_merge(
                $container->getParameter('templating.helper.form.resources'),
                array($formRessource)
            ));
        }

        if (in_array('twig', $templatingEngines)) {
            $formRessource = 'EWZUploaderBundle:Form:ewz_uploader_widget.html.twig';

            $container->setParameter('twig.form.resources', array_merge(
                $container->getParameter('twig.form.resources'),
                array($formRessource)
            ));
        }
    }
}
