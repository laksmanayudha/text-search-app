<?php 

class Connection{

    public static function get_connection($database){
         return mysqli_connect("localhost", "root", "", $database);
    }
}
