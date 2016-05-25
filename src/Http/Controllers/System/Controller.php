<?php

namespace KodiCMS\API\Http\Controllers\System;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use KodiCMS\API\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use KodiCMS\Support\Traits\Controller as ControllerTrait;
use KodiCMS\API\Http\Controllers\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ControllerTrait;

    /**
     * Массив возвращаемых значений, будет преобразован в формат JSON.
     * @var array
     */
    public $responseArray = ['content' => null];

    /**
     * Execute an action on the controller.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return array
     */
    public function callAction($method, $parameters)
    {
        $this->responseArray['type'] = Response::TYPE_CONTENT;
        $this->responseArray['method'] = $this->request->method();
        $this->responseArray['code'] = Response::NO_ERROR;

        if (isset($this->requiredFields[$method]) and is_array($this->requiredFields[$method])) {
            $this->validateParameters($this->requiredFields[$method]);
        }

        $this->before();
        $response = call_user_func_array([$this, $method], $parameters);
        $this->after();

        if ($response instanceof RedirectResponse) {
            $this->responseArray['type'] = Response::TYPE_REDIRECT;
            $this->responseArray['targetUrl'] = $response->getTargetUrl();
            $this->responseArray['code'] = $response->getStatusCode();
        } elseif ($response instanceof JsonResponse) {
            return $response;
        }

        return (new Response(config('app.debug')))->createResponse($this->responseArray);
    }
}
