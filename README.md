# Beauty JSON-RPC

## What is JSON-RPC?

**JSON-RPC** is a lightweight protocol for remote procedure calls (RPC) over HTTP(S) or any other transport, where all requests and responses are plain JSON.

* Official spec: [https://www.jsonrpc.org/specification](https://www.jsonrpc.org/specification)
* Minimalistic (no REST routes, just one endpoint)
* Allows calling any method with parameters, returns result or error
* Supports batch (multiple calls in one request)

---

## Installation

Install the package via Composer:

```bash
composer require beauty-framework/jsonrpc
```

---

## Registering Console Commands

Add the command provider:

```php
// config/commands.php
return [
    // ...
    \Beauty\JsonRPC\Console\RegisterCommands::commands(),
];
```

---

## Quick Start: Setup and Configuration

1. **Install config** (creates a template for handler auto-discovery):
    ```bash
    ./beauty jsonrpc:install
    ```
   > After this you’ll have a `config/json-rpc.php` file with auto-discovery settings for your RPC handlers.

   Example `config/json-rpc.php`:
    ```php
    <?php
    declare(strict_types=1);

    return [
        __DIR__ . '/../app/RpcHandlers/**/*.php',
    ];
    ```

    ---

   ### Register handlers in your worker

   After initializing your `$app` in `workers/http-worker.php`, add this line to register all discovered handlers:

    ```php
    $app = (new App(container: $application->containerManager->getContainer()))
        ->withRouterConfig($application->routerConfig)
        ->withMiddlewares($application->middlewares);

    \Beauty\JsonRPC\JsonRpcServer::setHandlers(require base_path('config/json-rpc.php')); // <-- this is required!

    while ($psrRequest = $worker->waitRequest()) {
        // ...
    }
    ```

   This is **required** for all your RpcHandlers to be visible to the JsonRpcServer.  
   Otherwise, your methods will not be available!

2. **Add the base JSON-RPC endpoint:**

   Create a controller:

   ```php
   <?php
   declare(strict_types=1);

   namespace App\Controllers;

   use Beauty\Core\Router\Route;
   use Beauty\Http\Enums\HttpMethodsEnum;
   use Beauty\Http\Request\HttpRequest;
   use Beauty\JsonRPC\JsonRpcServer;
   use Psr\Http\Message\ResponseInterface;

   class RpcController
   {
       public function __construct(
           protected JsonRpcServer $rpcServer,
       ) {}

       #[Route(HttpMethodsEnum::POST, '/rpc')]
       public function rpc(HttpRequest $request): ResponseInterface
       {
           return $this->rpcServer->handle($request);
       }
   }
   ```

   This will be your unified entrypoint for all JSON-RPC requests (`/rpc`).

3. **Generate your own RPC handler:**

   ```bash
   ./beauty generate:handler TestHandler
   ```

   > This will create a file at `app/RpcHandlers/TestHandler.php` with a basic handler skeleton.

4. **Define a method with the #\[RpcMethod] attribute:**

   ```php
   <?php
   declare(strict_types=1);

   namespace App\RpcHandlers;

   use Beauty\JsonRPC\Responses\RpcResponse;
   use Beauty\JsonRPC\RpcMethod;
   use Psr\Http\Message\ResponseInterface;

   class TestHandler
   {
       #[RpcMethod('test.test')]
       public function test(string $msg, array $names, string|int|null $id = null): ResponseInterface
       {
           return new RpcResponse([
               'msg' => $msg,
               'names' => $names,
           ], id: $id);
       }
   }
   ```

    * Method arguments are automatically mapped from the JSON-RPC `params`.
    * An `id` argument (if present) will always receive the request ID (for batch/tracing).
    * You can use DI for services, just typehint the argument (e.g., `LoggerInterface`).

---

## Example request to your server

```json
{
  "jsonrpc": "2.0",
  "method": "test.test",
  "params": {
    "msg": "Hello",
    "names": ["Alice", "Bob"]
  },
  "id": 42
}
```

**Response:**

```json
{
  "jsonrpc": "2.0",
  "result": {
    "msg": "Hello",
    "names": ["Alice", "Bob"]
  },
  "id": 42
}
```

---

## Useful Links

* JSON-RPC 2.0 Spec: [https://www.jsonrpc.org/specification](https://www.jsonrpc.org/specification)
* Github: [https://github.com/beauty-framework/jsonrpc](https://github.com/beauty-framework/jsonrpc)

---

## Why JSON-RPC?

* One entrypoint, no route mess
* Perfect for microservices, P2P, gRPC-like APIs
* Easy to implement batching, DI, versioning
* Simple frontend integration (works anywhere)
