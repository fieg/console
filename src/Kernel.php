<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Kernel
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var bool
     */
    protected $booted = false;

    public function boot()
    {
        if (true === $this->booted) {
            return;
        }

        // init container
        $this->container = $this->initializeContainer();

        $this->booted = true;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return ContainerInterface
     */
    protected function initializeContainer()
    {
        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../app/config'));
        $loader->load('config.yml');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../app/Resources/config'));
        $loader->load('services.yml');

        $container->set('kernel', $this);

        foreach ($this->getKernelParameters() as $name => $value) {
            $container->setParameter($name, $value);
        }

        $container->compile();

        return $container;
    }

    /**
     * Returns the kernel parameters.
     *
     * @return array An array of kernel parameters
     */
    protected function getKernelParameters()
    {
        return array_merge(
            array(
                //'kernel.root_dir' => realpath($this->rootDir) ?: $this->rootDir,
            ),
            $this->getEnvParameters()
        );
    }

    /**
     * Gets the environment parameters.
     *
     * Only the parameters starting with "SYMFONY__" are considered.
     *
     * @return array An array of parameters
     */
    protected function getEnvParameters()
    {
        $parameters = array();
        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'SYMFONY__')) {
                $parameters[strtolower(str_replace('__', '.', substr($key, 6)))] = $value;
            }
        }

        return $parameters;
    }
}
