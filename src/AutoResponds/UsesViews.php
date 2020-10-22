<?php

namespace SilvertipSoftware\AutoResponds\AutoResponds;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

trait UsesViews {

    /**
     * Set this to a specific view name in your controller actions if needed. Otherwise, we figure it out.
     */
    protected $viewNameForResponse = null;

    /**
     * Defaults to use the controller action to figure out view name. Setting this to false uses the route name.
     */
    protected $useActionForViewName = true;

    /**
     * Figure out the view name to use for this format. By default, non-html formats get a subdirectory named after the
     * format to store views in. This really only makes sense for view-based responses, obviously.
     *
     * @param  string $format
     * @return string
     */
    public function getViewNameForResponse($format) {
        $format = $format == 'html' ? null : $format;

        if ($this->viewNameForResponse == null) {
            $this->viewNameForResponse = $this->useActionForViewName ?
                $this->viewNameFromAction() : 
                Route::getCurrentRoute()->getName();
        }

        if ($format != null) {
            return preg_replace('/^(.*?)\.([^\.]*)$/', '$1.' . $format . '.$2', $this->viewNameForResponse);
        }

        return $this->viewNameForResponse;
    }

    /**
     * Kind of a hack to allow for customizing view that gets returned for long-press previews on mobile on
     * js enabled anchor tags... should be overridden to check if the js view exists for an html-format request, and
     * return an appropriate preview page. Or some way to prevent long-press previews...
     *
     * @return string|null
     */
    public function getViewNameForMissingView() {
        return null;
    }

    /**
     * Convert a controller action spec into a view name. Passing null uses the current route.
     *
     * The conversion is based off of the controller root namespace, and
     * walks the namespace hierarchy, separating by dots as it goes. For the default \App\Http\Controllers namespace:
     *
     *     AccountsController@index -> accounts.index
     *     Admin\SettingsController@show -> admin.settings.show
     *
     * and so on. This currently fails horribly for closure-based routes, so don't use those.
     *
     * @param  \Illuminate\Routing\Route|null $route
     * @return string
     */
    protected function viewNameFromAction($route = null) {
        $route = $route ?? Route::getCurrentRoute();
        $parts = explode('@', $route->getActionName());
        $controller = str_replace($this->controllerRootNamespace() . '\\', '', $parts[0]);
        
        $name = array_reduce(explode('\\', $controller), function ($memo, $dir) {
            $frag = str_replace('Controller', '', $dir);
            return $memo . strtolower(Str::snake($frag)) . '.';
        }, '');

        $name .= $parts[1];

        return $name;
    }

    /**
     * Returns an array of controller instance variables to share with the view. By default, this returns all of them.
     * For simplicity, this can just return an array of names, and we'll grab variable values. Otherwise, return an
     * associative array of name=>value pairs.
     *
     * @return array
     */
    protected function getVariablesToShare() {
        return get_object_vars($this);
    }

    /**
     * Share specified variables with the view. If the array is not associative, treat it as a list of names, and grab
     * the values here. Closures are called as well, so computed variables can be used as well.
     *
     * The special 'controller' variable gets automatically shared as well, which is a bit of a hack to allow for 
     * calling methods on the controller, rather than a whole facade type of infrastructure.
     */
    protected function shareControllerVariables() {
        $sharedVars = $this->getVariablesToShare();
        if (!Arr::isAssoc($sharedVars)) {
            $sharedVars = array_reduce($sharedVars, function ($memo, $name) {
                $memo[$name] = value($this->{$name});
                return $memo;
            }, []);
        }

        View::share(array_merge(
            ['controller' => $this],
            $sharedVars
        ));
    }
}
