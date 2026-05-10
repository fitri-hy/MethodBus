<?php

namespace MethodBus\Core;

use MethodBus\Loader\PluginLoader;
use MethodBus\Middleware\AuthMiddleware;
use MethodBus\Middleware\ValidationMiddleware;
use MethodBus\Security\FileNonceStore;
use MethodBus\Security\ReplayGuard;
use MethodBus\Security\SignatureVerifier;

final class Kernel
{
    private PluginManager $pluginManager;
    private Pipeline $pipeline;
    private PluginRunner $runner;
    private Logger $logger;
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();

        $this->container->singleton(
            FileNonceStore::class,
            FileNonceStore::class
        );

        $this->pluginManager = new PluginManager();

        (new PluginLoader($this->pluginManager))->load();

        $this->pipeline = new Pipeline([
            new AuthMiddleware(),
            new ReplayGuard(
                $this->container->make(FileNonceStore::class)
            ),
            new SignatureVerifier(),
            new ValidationMiddleware()
        ]);

        $this->runner = new PluginRunner(
            $this->container
        );

        $this->logger = new Logger();
    }

    public function execute(
        string $method,
        array $payload,
        array $context = []
    ): array {
        $request = empty($context)
            ? Request::capture()
            : Request::fromContext($payload, $context);

        $pluginClass = $this->pluginManager->resolve($method);

        $this->logger->log(
            $request->id(),
            'request_start',
            [
                'method' => $method
            ]
        );

        $payload = $this->pipeline->handle(
            $request,
            $payload
        );

        $result = $this->runner->run(
            $pluginClass,
            $payload
        );

        return Response::success(
            $result,
            $request->id()
        );
    }

    public function getPluginManager(): PluginManager
    {
        return $this->pluginManager;
    }
}