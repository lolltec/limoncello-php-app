<?php declare (strict_types=1);

namespace App\Web\Controllers;

use Limoncello\Common\Reflection\ClassIsTrait;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class BaseController
{
    use ControllerTrait, ClassIsTrait;
}
