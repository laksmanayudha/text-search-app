<?php 
class Document{

    public $path;
    public $name;

    public function __construct($path){
        $this->path = $path;

        // ambil nama file
        $arr = explode("/", $path);
        $this->name = end($arr);
    }

    public function get_all_word(){

        if( !file_exists($this->path)  ){
            return false;
        }

        // buka dokumen
        $myfile = fopen($this->path, "r") or die("Unable to open file!");

        // baca dokumen
        $strings = fread($myfile, filesize($this->path));

        // ambil setiap katanya, ubah ke dalam bentuk array
        $words = str_word_count($strings, 1);

        // tutup dokumen
        fclose($myfile);

        return $words;

        
    }
}