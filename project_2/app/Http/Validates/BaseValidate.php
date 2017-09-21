<?php

namespace App\Http\Validates;


class BaseValidate  {

    public $PATTERN_IS_NUMBER = '/^[0-9]+$/';
    public $PATTERN_FORMAT_EMAIL = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

}
