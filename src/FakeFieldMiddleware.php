<?php

namespace Codemen\FakeField;

use Closure;

class FakeFieldMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $app = app('FakeField');
        $allFields = $app->setFakeFields($request);
        $app->setOriginalFieldInRequest($request, $allFields);
        $response = $next($request);

        if ($request->ajax() && $response->status() == 422) {
            $fakeFields = config('fakefield.input_keys');
            $newErrors = [];
            foreach ($response->getData(true)['errors'] as $key => $value) {
                $key = isset($fakeFields[$key]) ? $fakeFields[$key] : $key;
                $newErrors[$key] = $value;
            }

            $response->setData(['errors' => $newErrors]);
        }

        return $response;
    }


}
