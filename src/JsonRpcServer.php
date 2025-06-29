<?php
declare(strict_types=1);

namespace Beauty\JsonRPC;

use Beauty\Http\Request\HttpRequest;
use Beauty\Http\Response\JsonResponse;
use Beauty\JsonRPC\Responses\InvalidRequestErrorRpcResponse;
use Beauty\JsonRPC\Responses\MethodNotFoundErrorRpcResponse;
use Beauty\JsonRPC\Responses\ParseErrorRpcResponse;
use Beauty\JsonRPC\Responses\RpcResponse;
use Beauty\JsonRPC\Responses\ServerErrorRpcResponse;
use Beauty\JsonRPC\Validators\RpcSchemaValidator;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Finder\Finder;

class JsonRpcServer
{
    protected static array $handlers = [];

    public function __construct(
        protected ContainerInterface $container,
        protected LoggerInterface $logger,
    )
    {
    }

    public static function setHandlers(array $paths): void
    {
        $finder = new Finder();
        foreach ($paths as $path) {
            $dir = dirname($path);
            $pattern = basename($path);

            $finder->files()->in(dirname($dir))->name($pattern)->depth('>= 0');
        }

        $loadedClassesBefore = get_declared_classes();

        foreach ($finder as $file) {
            require_once $file->getRealPath();

            $loadedClasses = array_diff(get_declared_classes(), $loadedClassesBefore);
            foreach ($loadedClasses as $class) {
                $ref = new ReflectionClass($class);
                if ($ref->isAbstract() || $ref->isInterface()) continue;

                foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    foreach ($method->getAttributes(RpcMethod::class) as $attr) {
                        $meta = $attr->newInstance();
                        self::$handlers[$meta->name] = [$class, $method->getName()];
                    }
                }
            }
            $loadedClassesBefore = get_declared_classes();
        }
    }

    public function handle(HttpRequest $request): ResponseInterface
    {
        try {
            $payload = json_decode((string) $request->getBody(), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return new ParseErrorRpcResponse();
        }

        if (is_array($payload) && array_is_list($payload)) {
            if (count($payload) === 0) {
                return new JsonResponse(200, []);
            }

            $responses = [];
            foreach ($payload as $item) {
                $responses[] = $this->validateAndDispatch($item);
            }

            return new JsonResponse(200, $responses);
        }

        return $this->validateAndDispatch($payload);
    }

    private function dispatch(array $rpcRequest): ResponseInterface
    {
        $id = $rpcRequest['id'] ?? null;
        $method = $rpcRequest['method'] ?? null;
        $params = $rpcRequest['params'] ?? [];

        if (!isset(self::$handlers[$method])) {
            return new MethodNotFoundErrorRpcResponse(id: $id);
        }

        [$className, $methodName] = self::$handlers[$method];

        try {
            $object = $this->container->get($className);

            return $this->callWithDi($object, $methodName, $params, $id);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            return new ServerErrorRpcResponse($e->getMessage(), id: $id);
        }
    }

    private function validateAndDispatch(array $data): ResponseInterface
    {
        if (!RpcSchemaValidator::isValidRequest($data)) {
            return new InvalidRequestErrorRpcResponse(id: $data['id'] ?? null);
        }

        return $this->dispatch($data);
    }

    private function callWithDi($object, string $methodName, array $params = [], string|int|null $id = null): mixed
    {
        $method = new \ReflectionMethod($object, $methodName);
        $arguments = [];

        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            $type = $param->getType();

            if ($name === 'id') {
                $arguments[] = $id;
            }
            elseif (array_key_exists($name, $params)) {
                $arguments[] = $params[$name];
            }
            elseif ($type && !$type->isBuiltin()) {
                $dependency = $this->container->get($type->getName());
                $arguments[] = $dependency;
            }
            elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            }
            else {
                $arguments[] = null;
            }
        }

        return $method->invokeArgs($object, $arguments);
    }

}