<?php 

class Snippet{
    
    public $highlights;
    public $snippet;
    public $max_word_before;
    public $max_word_after;
    public $str_before;
    public $str_after;

    public function __construct($max_word_after = 5, $max_word_before=6){

        $this->max_word_before = $max_word_before;
        $this->max_word_after = $max_word_after;

    }

    public function get_snippet($document, $snippet_key){
        $words = $document->get_all_word();
        $keyword = $words[$snippet_key];
        $str_after = array_slice($words, $snippet_key+1, $this->max_word_after);
        $str_before = array_slice($words, $snippet_key-$this->max_word_before, $this->max_word_before);

        $str_after = implode(" ", $str_after);
        $str_before = implode(" ", $str_before);

        $join = $str_before . " " . $keyword . " " . $str_after;

        $this->snippet = $join;
        $this->highlights =$keyword;
        $this->str_before = $str_before;
        $this->str_after = $str_after;

        return $this;

    }

}