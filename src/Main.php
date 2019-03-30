<?php

namespace Codemen\FakeField;

use Illuminate\Support\Str;
use ReflectionClass;

class Main
{
    public function setFakeFields($request)
    {
        $models = $this->getModels();
        $base_request_key = $request->get('_fake_key');
        if ($base_request_key != null) {
            $base_request_key = $this->decrypt($base_request_key);
        }
        if (
            $base_request_key != null) {
            $base_key = $serial = $base_request_key;
        } else {
            $base_key = $serial = rand(1, 500);
        }
        $prefix = config('fakefield.prefix');
        $tableKeys = [];
        foreach ($models as $model) {
            $table = '';
            $fields = $this->accessProtected($model, 'fakeFields');
            if (empty($fields)) {
                continue;
            }
            foreach ($fields as $field) {
                if (!array_key_exists($field, $tableKeys)) {
                    $tableKeys[$table . $field] = $prefix . $serial;

                    $serial++;
                }
            }
        }

        $allFields = [
            'table_keys' => $tableKeys,
            '_base_key' => $base_key
        ];
        config()->set('fakefield', $allFields);
        return $allFields;
    }

    private function getModels()
    {
        $models = [];
        $modelPath = config('fakefield.model_path');
        $path = base_path($modelPath);
        foreach (glob($path . '/*') as $file) {
            if (is_dir($file)) {
                $dir = $file;
                foreach (glob($dir . '/*.php') as $file) {
                    $className = str_replace('/', '\\', Str::studly($modelPath)) . '\\' . basename($dir) . '\\' . basename($file, '.php');
                    array_push($models, new $className());
                }
            } else {
                $className = str_replace('/', '\\', Str::studly($modelPath)) . '\\' . basename($file, '.php');
                array_push($models, new $className());
            }
        }
        return $models;
    }

    private function decrypt($value)
    {
        $encValue = json_decode(base64_decode($value), true);
        $cipher = config('app.cipher');
        $encKey = openssl_digest(php_uname(), 'SHA256', TRUE);
        $decValue = openssl_decrypt($encValue['value'], $cipher, $encKey, 0, hex2bin($encValue['iv']));
        unset($value, $cipher, $encKey, $encValue);
        return $decValue;
    }

    private function accessProtected($obj, $prop)
    {
        $reflection = new ReflectionClass($obj);
        if ($reflection->hasProperty($prop)) {
            $property = $reflection->getProperty($prop);
            $property->setAccessible(true);
            return $property->getValue($obj);
        } else {
            return [];
        }

    }

    public function setOriginalFieldInRequest($request, $allFields)
    {
        $fakeFields = array_flip($allFields['table_keys']);
        $inputs = $request->all();
        $newRequestArray = [];
        $inputKeys = [];
        $isFileExists = false;
        foreach ($inputs as $key => $value) {
            if (array_key_exists($key, $fakeFields)) {

                $inputKeys[$fakeFields[$key]] = $key;

                if ($request->hasFile($key)) {
                    $newRequestFileArray[$fakeFields[$key]] = $value;
                    $request->files->remove($key);
                    $isFileExists = true;
                } else {
                    $newRequestArray[$fakeFields[$key]] = $value;
                    $request->request->remove($key);
                }

            }
        }
        $request->merge($newRequestArray);

        if ($isFileExists) {
            $request->files->replace($newRequestFileArray);
        }

        config()->set('fakefield.input_keys', $inputKeys);
    }

    function create($fieldName, $reverse = false)
    {
        if ($reverse === true) {
            return array_flip(config('fakefield.table_keys'))[$fieldName];
        }
        return config()->get('fakefield.table_keys.' . $fieldName, $fieldName);
    }

    public function getBaseKey()
    {
        return $this->encrypt(config('fakefield._fake_key'));
    }

    private function encrypt($value)
    {
        $cipher = config('app.cipher');
        $encKey = openssl_digest(php_uname(), 'SHA256', TRUE);
        $encIV = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encValue = openssl_encrypt($value, $cipher, $encKey, 0, $encIV);
        $baseValue = json_encode(['iv' => bin2hex($encIV), 'value' => $encValue]);
        unset($token, $cipher, $encKey, $encIV, $encValue);

        return base64_encode($baseValue);
    }
}
