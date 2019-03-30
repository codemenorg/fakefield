<?php

if (!function_exists('fake_field')) {
    function fake_field($fieldName)
    {
        $fakeField = new \Codemen\FakeField\Main();
        return $fakeField->create($fieldName);
    }
}

if (!function_exists('fake_key')) {
    function fake_key()
    {
        $fakeField = new \Codemen\FakeField\Main();
        return $fakeField->getBaseKey();
    }
}
