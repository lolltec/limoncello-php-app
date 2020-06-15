<?php declare (strict_types=1);

namespace App\Routes;

use App\Container\RequestStorageConfigurator;
use App\Web\Middleware\CatchAllResponsesMiddleware;
use Limoncello\Contracts\Application\RoutesConfiguratorInterface;
use Limoncello\Contracts\Routing\GroupInterface;
use Limoncello\Flute\Http\Traits\FluteRoutesTrait;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WebRoutes implements RoutesConfiguratorInterface
{
    const TOP_GROUP_PREFIX = '';

    use FluteRoutesTrait;

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function configureRoutes(GroupInterface $routes): void
    {
        // Every group, controller and even method may have custom `Request` factory and `Container` configurator.
        // Thus container for `API` and `Web` groups can be configured differently which could be used for
        // improving page load time for every HTTP route.
        // Container can be configured even for individual controller method (e.g. `PaymentsController::index`).
        // Also custom middleware could be specified for a group, controller or method.

        $routes
            // HTML pages group
            // This group uses exception handler to provide error information in HTML format with Whoops.
            ->group(self::TOP_GROUP_PREFIX, function (GroupInterface $routes): void {

                $routes->addContainerConfigurators([
                    RequestStorageConfigurator::CONFIGURATOR,
                ])->addMiddleware([]);

            });
    }

    /**
     * This middleware will be executed on every request even when no matching route is found.
     *
     * @return string[]
     */
    public static function getMiddleware(): array
    {
        return [
            CatchAllResponsesMiddleware::class,
        ];
    }
}
