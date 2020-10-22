<?php

namespace SilvertipSoftware\AutoResponds\AutoResponds;

use Illuminate\Support\Facades\View;
use InvalidArgumentException;

trait WithHtml {

    /**
     * Auto find and create a standard view-based response. The fallback mechanism is a hack for long-press previews 
     * on mobile for non-HTML requests. There's got to be a better way to handle them, but so far this is it.
     *
     * @param  int $status
     * @return \Illuminate\Http\Response
     */
    public function createHtmlResponse($status = 200) {
        $viewName = $this->getViewNameForResponse('html');

        if (!View::exists($viewName)) {
            $fallbackViewName = $this->getViewNameForMissingView();
            if ($fallbackViewName && View::exists($fallbackViewName)) {
                $viewName = $fallbackViewName;
            } else {
                throw new InvalidArgumentException("View [{$viewName}] not found.");
            }
        }

        $this->shareControllerVariables();
        return response()->view($viewName, [], $status);
    }
}
