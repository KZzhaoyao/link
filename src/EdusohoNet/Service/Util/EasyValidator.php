<?php

namespace EdusohoNet\Service\Util;

use \RuntimeException;

class EasyValidator
{
    protected $rules;

    protected $filter;

    /**
     * @param [type]  $rules  校验规则
     */
    public function __construct ($rules)
    {
        $this->rules = $rules;
    }

    public function validate(array $fields, $filter = true)
    {
        $filteredFields = array();
        $errors = array();
        foreach ($this->rules as $key => $rules) {
            $rules = explode(' ', $rules);
            $keyErrors = array();
            foreach ($rules as $rule) {
                $result = $this->callValidateFunction($rule, $fields, $key);
                if ($result !== true) {
                    $keyErrors["{$key}:{$rule}"] = $result;
                }
            }

            if ($keyErrors) {
                $errors = array_merge($errors, $keyErrors);
            } else {
                if (isset($fields[$key])) {
                    $filteredFields[$key] = $fields[$key];
                }
            }
        }

        if ($errors) {
            return array(null, $errors);
        } else {
            return array($filter == true ? $filteredFields : $fields, null);
        }
    }

    public function getErrorMessages(array $errors)
    {
        return implode('; ', $errors);
    }

    public function addRule($name, $rule, $replace = false)
    {

    }

    protected function callValidateFunction($name, $fields, $key)
    {
        if (!in_array($name, array('required', 'optional'))) {
            if ($this->validateRequired($fields, $key) !== true) {
                return true;
            }
        }

        $methodName = 'validate' . ucfirst($name);
        if (!method_exists($this, $methodName)) {
            throw new \RuntimeException("{$name} rule is not exist.");
        }

        return $this->{$methodName}($fields, $key);
    }

    protected function validateRequired($fields, $key)
    {
        if (!isset($fields[$key]) || is_null($fields[$key]) || $fields[$key] === '') {
            return "{$key}不存在";
        }
        return true;
    }

    protected function validateOptional($fields, $key)
    {
        return true;
    }

    protected function validateEmail($fields, $key)
    {
        $value = (string) $fields[$key];
        $valid = filter_var($value, FILTER_VALIDATE_EMAIL);
        if ($valid === false) {
            return "Email格式不正确";
        }
        return true;
    }

    protected function validateUrl($fields, $key)
    {

        if (!preg_match('/^(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/', $fields[$key])) {
            return "URL不正确";
        }

        return true;
    }

    protected function validateQq($fields, $key)
    {
        if (!preg_match('/^[1-9]\d{4,}$/', $fields[$key])) {
            return "QQ号码格式不正确";
        }

        return true;
    }


    protected function validateMobile($fields, $key)
    {
        if (!preg_match('/^1\d{10}$/', $fields[$key])) {
            return "手机号格式不正确";
        }

        return true;
    }

}