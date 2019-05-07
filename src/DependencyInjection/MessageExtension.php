<?php


namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class MessageExtension extends Extension implements PrependExtensionInterface
{
    use PersistenceExtensionTrait;

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        if ($container->hasExtension('sulu_admin')) {
            $container->prependExtensionConfig(
                'sulu_admin',
                [
                    'lists' => [
                        'directories' => [
                            __DIR__ . '/../Resources/config/lists',
                        ],
                    ],
                    'forms' => [
                        'directories' => [
                            __DIR__ . '/../Resources/config/forms',
                        ],
                    ],
                    'resources' => [
                        'contacts' => [
                            'routes' => [
                                'list' => 'get_contacts',
                                'detail' => 'get_contact',
                            ],
                        ]
                    ]
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('content.xml');
        $loader->load('command.xml');
        $container->setParameter(
            'sulu_contact.defaults',
            $config['defaults']
        );
        $this->setDefaultForFormOfAddress($config);
        $container->setParameter(
            'sulu_contact.form_of_address',
            $config['form_of_address']
        );
        $container->setParameter(
            'sulu_contact.contact_form.category_root',
            $config['form']['contact']['category_root']
        );
        $container->setParameter(
            'sulu_contact.account_form.category_root',
            $config['form']['account']['category_root']
        );
        $this->configurePersistence($config['objects'], $container);
    }

}
