<?php

use Illuminate\Bus\Dispatcher;
use Illuminate\Foundation\Bus\PendingDispatch;

if ( ! function_exists('dispatch')) {
    /**
     * Dispatch a job to its appropriate handler.
     *
     * @param mixed $job
     *
     * @return PendingDispatch
     */
    function dispatch($job): \Illuminate\Foundation\Bus\PendingDispatch
    {
        if ($job instanceof Closure) {
            $job = new CallQueuedClosure(new SerializableClosure($job));
        }

        return new PendingDispatch($job);
    }
}
if ( ! function_exists('dispatch_now')) {
    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param mixed $job
     * @param mixed $handler
     *
     * @return mixed
     */
    function dispatch_now($job, $handler = null)
    {
        return app(Dispatcher::class)->dispatchNow($job, $handler);
    }
}
