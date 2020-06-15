<?php declare (strict_types=1);

namespace Settings;

use Limoncello\Templates\Package\TemplatesSettings;

/**
 * @package Settings
 */
class Templates extends TemplatesSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        $appRootFolder   = implode(DIRECTORY_SEPARATOR, [__DIR__, '..']);
        $templatesFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'public', 'dist']);
        $cacheFolder     = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'cache', 'templates']);

        $settings = [

                static::KEY_APP_ROOT_FOLDER  => $appRootFolder,
                static::KEY_TEMPLATES_FOLDER => $templatesFolder,
                static::KEY_CACHE_FOLDER     => $cacheFolder,

            ] + parent::getSettings();

        return array_replace([
            static::KEY_TEMPLATES_FILE_MASK => '*.html',
        ], $settings);
    }
}
