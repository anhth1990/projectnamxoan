<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AccountDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_account");
    }

}

?>