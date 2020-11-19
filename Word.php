<?php 

class Word{

    public $value;
    public $letters;
    public $len;
    public static $count;
    

    public function __construct($string, $count = -1){

        $this->value = $string;
        $this->letters = str_split($string);
        self::$count = $count;
        $this->len = strlen($string);
    }

    public function next(){
        self::$count++;
        if( self::$count < $this->len ){
            return $this->letters[self::$count];
        }
        
    }

    public function prev(){
        self::$count--;
        if( self::$count > -1){
            return $this->letters[self::$count];
        }
    }

    public function curr(){
        return $this->letters[self::$count];
    }

    public function reset(){
        self::$count=-1;
    }

    public function hasNext(){
        
        if ( !empty($this->next()) ){
            $this->prev();
            return true;
        }else{
            $this->prev();
            return false;
        }
    }

}