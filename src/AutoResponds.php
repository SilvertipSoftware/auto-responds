<?php

namespace SilvertipSoftware\AutoResponds;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use SilvertipSoftware\AutoResponds\AutoResponds\UsesViews;
use SilvertipSoftware\AutoResponds\AutoResponds\WithHtml;
use SilvertipSoftware\AutoResponds\AutoResponds\WithJavascript;
use SilvertipSoftware\AutoResponds\AutoResponds\WithJson;

trait AutoResponds {
    use UsesViews, WithHtml, WithJavascript, WithJson;

    /**
     * Hook into the controller action call, so we can adjust responses. This can't be middleware, since we want to
     * access the controller for a lot of things. A mini "middleware" pipeline for controller actions would be nice...
     *
     * @param  string $method
     * @param  array  $parameters
     * @return \Illuminate\Http\Response
     */
    public function callAction($method, $parameters) {
        $request = request();
        $request->controller = $this;

        // original callAction()
        $response = call_user_func_array([$this, $method], $parameters);

        if ($response instanceof RedirectResponse) {
            $response = $this->mapRedirectResponse($request, $response);
        }

        if ($response === null) {
            $response = $this->createResponse($request);
        }

        return $response;
    }

    /**
     * The format of the response that the request is looking for. responseFormat can be used if format() doesn't
     * give the correct answer. (eg. for extensions in the URL: /projects/12345.json. This needs custom middleware,
     * however; we don't provide it).
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    protected function desiredResponseFormat($request) {
        return $request->responseFormat ?? $request->format() ?? 'html';
    }

    /**
     * Some formats want to treat redirects differently (eg. JS), so allow them to be mapped to an alternate response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Http\RedirectResponse $response
     * @return \Illuminate\Http\Response
     */
    protected function mapRedirectResponse($request, $response) {
        $methodName = 'mapRedirectFor' . ucfirst($this->desiredResponseFormat($request));

        if (method_exists($this, $methodName)) {
            $response = $this->{$methodName}($response);
        }

        return $response;
    }

    /**
     * Auto-create a response if the controller didn't explicitly return one. Delegate to format-specific methods.
     * We also handle 304 Not Modified responses here, if that macro is installed on the Request. Again, this couldn't
     * be middleware, but would be nice if it was mini-middleware surrounding callAction().
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function createResponse($request) {
        // don't send anything if client has fresh data
        if (Request::hasMacro('isFresh') && $request->isFresh()) {
            return response(null)->setNotModified();
        }

        $response = null;

        $methodName = 'create' . ucfirst($this->desiredResponseFormat($request)) . 'Response';
        if (method_exists($this, $methodName)) {
            $response = $this->{$methodName}();
        }

        return $response;
    }

    /**
     * Base Laravel doesn't seem to have a way to access this, so we need to duplicate the namespace here. Sigh.
     *
     * @return string
     */
    protected function controllerRootNamespace() {
        return 'App\Http\Controllers';
    }
}
