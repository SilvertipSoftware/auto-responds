<?php

namespace SilvertipSoftware\AutoResponds\AutoResponds;

trait WithJavascript {

    /**
     * Auto find and create a Javascript response based on the controller action or route name.
     *
     * @param  int $status
     * @return \Illuminate\Http\Response
     */
    public function createJsResponse($status = 200) {
        $viewName = $this->getViewNameForResponse('js');

        return $this->createJsResponseFromView($viewName, [], $status);
    }

    /**
     * Convert a redirect response into some application-specific javascript. Could be just a simple 
     *     window.location.href = ...
     * statement, but any javascript required can be returned.
     *
     * @param  \Illuminate\Http\RedirectResponse $response
     * @param  int $status
     * @return \Illuminate\Http\Response
     */
    public function mapRedirectForJs($response, $status = 200) {
        $viewName = $this->getJsRedirectViewName();
        $data = [
            'redirectToUrl' => $response->getTargetUrl()
        ];

        return $this->createJsResponseFromView($viewName, $data, $status);
    }

    /**
     * Create a javascript response from a given view. The javascript is auto-wrapped, so the view does not have to
     * worry about that itself.
     *
     * @param  string $viewName
     * @param  array  $data
     * @param  int    $status
     * @return \Illuminate\Http\Response
     */
    public function createJsResponseFromView($viewName, $data = [], $status = 200) {
        $this->shareControllerVariables();
        $content = view($viewName, $data)->render();

        return $this->createJsResponseWithContent($content, $status);
    }

    /**
     * Create a response by wrapping and setting the approprate Content-Type for a block of javascript.
     *
     * @param  string $content
     * @param  int    $status
     * @return \Illuminate\Http\Response
     */
    public function createJsResponseWithContent($content, $status) {
        return response($this->wrapJsContent($content), $status)
            ->header('Content-Type', 'text/javascript');
    }

    /**
     * Standard javascript wrapping. Can override if you need to do more on every javascript response.
     *
     * @param  string $content
     * @return string
     */
    protected function wrapJsContent($content) {
        return "(function() {\n". $content . "\n})();";
    }

    /**
     * Return the name of the view that handles javascript redirects. This view gets passed a 'redirectToUrl' variable
     * which is the location specified in the RedirectResponse
     *
     * @return string 
     */
    protected function getJsRedirectViewName() {
        return 'js_redirect';
    }
}
