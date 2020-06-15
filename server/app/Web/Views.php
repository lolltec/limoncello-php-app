<?php declare (strict_types=1);

namespace App\Web;

/**
 * @package App\L10n
 */
interface Views
{
    /**
     * Namespace name for mapping template IDs with localized templates.
     *
     * see `server/resources/messages/{LANG}/App.Views.Pages.php`
     */
    const NAMESPACE = 'App.Views.Pages';

    /** Template ID. */
    const DISTRIBUTABLE = 0;
}
