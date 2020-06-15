<?php declare (strict_types=1);

namespace Settings;

namespace App\Web\Middleware;

use App\Web\Controllers\ControllerTrait;
use App\Web\Views;
use Closure;
use Laminas\Diactoros\Response\HtmlResponse;
use Limoncello\Contracts\Application\MiddlewareInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
class CatchAllResponsesMiddleware implements MiddlewareInterface
{
    use ControllerTrait;

    /**
     * Middleware handler.
     */
    const CALLABLE_HANDLER = [self::class, self::MIDDLEWARE_METHOD_NAME];

    /**
     * @inheritdoc
     */
    public static function handle(
        ServerRequestInterface $request,
        Closure $next,
        ContainerInterface $container
    ): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $next($request);

        // error responses might have just HTTP 4xx code as well
        switch ($response->getStatusCode()) {
            case 404:
                return static::createResponseFromTemplate($container, Views::DISTRIBUTABLE, 404);
            default:
                return $response;
        }
    }

    /**
     * @param ContainerInterface $container
     * @param int                $templateId
     * @param int                $httpCode
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function createResponseFromTemplate(
        ContainerInterface $container,
        int $templateId,
        int $httpCode
    ): ResponseInterface
    {
        $body = static::view($container, $templateId);

        return new HtmlResponse($body, $httpCode);
    }
}
