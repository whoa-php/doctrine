<?php namespace Limoncello\Commands;

/**
 * Copyright 2015-2017 info@neomerx.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Limoncello\Application\Packages\Application\ApplicationSettings;
use Limoncello\Application\Traits\SelectClassImplementsTrait;
use Limoncello\Commands\Exceptions\ApplicationClassNotFoundException;
use Limoncello\Commands\Exceptions\ContainerMethodNotFoundException;
use Limoncello\Contracts\Container\ContainerInterface;
use Limoncello\Contracts\Provider\ProvidesCommandsInterface;
use Limoncello\Contracts\Settings\SettingsProviderInterface;

/**
 * @package Limoncello\Commands
 */
class ComposerPlugin implements PluginInterface, Capable
{
    use SelectClassImplementsTrait;

    /** Expected key at `composer.json` -> "extra" */
    const COMPOSER_JSON__EXTRA__APPLICATION = 'application';

    /** Expected key at `composer.json` -> "extra" -> "application" */
    const COMPOSER_JSON__EXTRA__APPLICATION__CLASS = 'class';

    /** Default application class name if not replaced via "extra" -> "application" -> "class" */
    const DEFAULT_APPLICATION_CLASS_NAME = '\\App\\Application';

    /** Method to be used to get application settings */
    const DEFAULT_APPLICATION_CREATE_CONTAINER_METHOD = 'createConfiguredContainer';

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IOInterface
     */
    private $ioInterface;

    /**
     * @inheritdoc
     */
    public function activate(Composer $composer, IOInterface $ioInterface)
    {
        $this->setComposer($composer)->setIoInterface($ioInterface)->loadCommands();
    }

    /**
     * @inheritdoc
     */
    public function getCapabilities()
    {
        return [
            CommandProvider::class => ComposerCommandProvider::class,
        ];
    }

    /**
     * @return IOInterface
     */
    protected function getIoInterface(): IOInterface
    {
        return $this->ioInterface;
    }

    /**
     * @param IOInterface $ioInterface
     *
     * @return ComposerPlugin
     */
    protected function setIoInterface(IOInterface $ioInterface): ComposerPlugin
    {
        $this->ioInterface = $ioInterface;

        return $this;
    }

    /**
     * @return Composer
     */
    protected function getComposer(): Composer
    {
        return $this->composer;
    }

    /**
     * @param Composer $composer
     *
     * @return ComposerPlugin
     */
    protected function setComposer(Composer $composer): ComposerPlugin
    {
        $this->composer = $composer;

        return $this;
    }

    protected function loadCommands()
    {
        $container = $this->getAppContainer();

        /** @var SettingsProviderInterface $settingsProvider */
        $provider = $container->get(SettingsProviderInterface::class);

        // Application settings have a list of providers which might have additional settings to load
        $appSettings     = $provider->get(ApplicationSettings::class);
        $providerClasses = $appSettings[ApplicationSettings::KEY_PROVIDER_CLASSES];
        foreach ($this->selectProviders($providerClasses, ProvidesCommandsInterface::class) as $providerClass) {
            /** @var ProvidesCommandsInterface $providerClass */
            foreach ($providerClass::getCommands() as $command) {
                new LimoncelloCommand($command);
            }
        }
    }

    /**
     * @return ContainerInterface
     */
    protected function getAppContainer(): ContainerInterface
    {
        $extra    = $this->getComposer()->getPackage()->getExtra();
        $appClass =
            $extra[static::COMPOSER_JSON__EXTRA__APPLICATION][static::COMPOSER_JSON__EXTRA__APPLICATION__CLASS] ??
                static::DEFAULT_APPLICATION_CLASS_NAME;
        if (class_exists($appClass) === false) {
            throw new ApplicationClassNotFoundException($appClass);
        }

        $application = new $appClass();
        if (method_exists($application, $methodName = static::DEFAULT_APPLICATION_CREATE_CONTAINER_METHOD) === false) {
            throw new ContainerMethodNotFoundException($methodName);
        }

        $container = $application->{$methodName};
        assert($container instanceof ContainerInterface);

        return $container;
    }
}
