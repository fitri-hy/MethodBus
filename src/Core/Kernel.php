<?php

namespace MethodBus\Core;

use MethodBus\Loader\PluginLoader;
use MethodBus\Middleware\AuthMiddleware;
use MethodBus\Middleware\ValidationMiddleware;
use MethodBus\Security\SignatureVerifier;

final class Kernel
{
    private PluginManager $pluginManager;
    private Pipeline $pipeline;
    private PluginRunner $runner;
    private Logger $logger;

    public function __construct()
    {
        $this->pluginManager = new PluginManager();

        (new PluginLoader($this->pluginManager))->load();

        $this->pipeline = new Pipeline([
            new AuthMiddleware(),
			new SignatureVerifier(),
            new ValidationMiddleware()
        ]);

        $this->runner = new PluginRunner();
        $this->logger = new Logger();
    }

    public function execute(string $method, array $payload, array $context = []): array
    {
        $request = empty($context)
            ? Request::capture()
            : Request::fromContext($payload, $context);

        $plugin = $this->pluginManager->resolve($method);

        $this->logger->log($request->id(), 'request_start', [
            'method' => $method
        ]);

        $payload = $this->pipeline->handle($request, $payload);

        file_put_contents(
            dirname(__DIR__, 2) . '/pipeline_debug.log',
            "PIPELINE HIT " . json_encode($payload) . PHP_EOL,
            FILE_APPEND
        );

        $result = $this->runner->run($plugin, $payload);

        return Response::success($result, $request->id());
    }

    public function getPluginManager(): PluginManager
    {
        return $this->pluginManager;
    }
}