<?php

namespace SilvertipSoftware\AutoResponds\AutoResponds;

use App\Libs\Routing\RouterMixins;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RuntimeException;

trait WithJson {

    /**
     * Auto create a JSON response, using JsonResource classes if found, otherwise a raw json response is returned.
     *
     * @param  int $status
     * @return \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function createJsonResponse($status = 200) {
        $propName = $this->getModelNameForResponse();
        $dataValue = $this->{$propName} ?? null;

        $resource = $this->jsonResourceFor($propName);
        $wrapper = $this->wrapperForResource($resource, $propName);

        if ($resource) {
            $resource::wrap($wrapper);
            return $resource;
        }

        return response()->json(
            [$wrapper => $dataValue],
            $status
        );
    }

    /**
     * Determine the model/variable name to use for this json response. Currently, by default use the route name,
     * assuming that the last sections are <model>.<operation>. Can also provide a getSubjectResourceTag() method 
     * which returns a specific model name. The model name will be pluralized if this is an index operation.
     *
     * This should be brought in-line with view name discovery as in UsesViews.
     *
     * @return string
     */
    protected function getModelNameForResponse() {
        $parts = explode('.', Route::getCurrentRoute()->getName());
        $operation = array_pop($parts);

        if (method_exists($this, 'getSubjectResourceTag')) {
            $modelName = $this->getSubjectResourceTag();
        } else {
            $modelName = array_pop($parts);
        }

        return ($operation == 'index') ? Str::plural($modelName) : Str::singular($modelName);
    }

    /**
     * Discover the appropriate JsonResource to use for a named model. A property with name name must already
     * be set on this controller, so we can vary based on singular vs. collection resources.
     *
     * If no resource classes fit the bill, return null.
     *
     * @param  string $name
     * @return Illuminate\Http\Resources\Json\JsonResource|null
     */
    protected function jsonResourceFor($name) {
        $value = $this->{$name} ?? null;

        if ($value instanceof JsonResource) {
            return $value;
        }

        if ($value instanceof Collection) {
            $clz = $this->resourceClassNameFor($name, false);
            if (class_exists($clz)) {
                return new $clz($value);
            }

            $clz = $this->resourceClassNameFor($name, true);
            if (class_exists($clz)) {
                return $clz::collection($value);
            }
        }

        $clz = $this->resourceClassNameFor($name, true);
        if (class_exists($clz)) {
            return new $clz($value);
        }

        return null;
    }

    /**
     * Return the tag used for wrapping JSON responses. We default to Laravel/JSON:api standard 'data', but this allows
     * customization application-wide.
     *
     * @param  Illuminate\Http\Resources\Json\JsonResource $resource
     * @param  string $propName
     * @return string
     */
    protected function wrapperForResource($resource, $propName)
    {
        return 'data';
    }

    /**
     * Determine the standard resource class name for a given model name, and whether we are looking for the singular 
     * or collection resource class name.
     *
     * Laravel docs standardize these based on the same namespace as controllers only Controllers -> Resources. eg:
     *
     *     App\Http\Controllers\AccountsController -> App\Http\Resources\AccountResource (or Collection)
     *     App\Http\Controllers\Admin\SettingsController -> App\Http\Resources\Admin\SettingsResource (or Collection)
     *
     * so we do the same.
     *
     * @param  string $name
     * @param  bool   $singular
     * @return string
     */
    protected function resourceClassNameFor($name, $singular) {
        $controllerNamespace = $this->controllerRootNamespace();
        $resourceNamespace = str_replace('Controllers', 'Resources', $controllerNamespace);

        $fullClz = get_class($this);
        $basename = class_basename($this);

        return str_replace(
            $basename,
            Str::studly(Str::singular($name)) . ($singular ? 'Resource' : 'Collection'),
            str_replace($controllerNamespace, $resourceNamespace, $fullClz)
        );
    }
}
