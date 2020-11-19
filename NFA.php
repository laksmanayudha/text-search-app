<?php 

require('Word.php');

class NFA{

    public $initialState;
    public $finalState;
    public $transitions;
    public $allState;
    public $sigma;

    public function __construct($initialState, $finalState, $transitions, $allState, $sigma){
        
        $this->initialState = $initialState;
        $this->finalState = $finalState;
        $this->transitions = $transitions;
        $this->allState = $allState;
        $this->sigma = $sigma;

    }

    public function result($document){

        // baca semua kata
        $words = $document->get_all_word();
        
        // baca satu kata
        foreach( $words as $position => $word ){

            if ( $this->accept($word) ){
                // echo $word . " is accepted <br>";

                return [
                    "accepted" => true,
                    "snippet_key" => $position
                ];
                break;
            }
        }

        return [
            "accepted" => false
        ];;

    }

    public function accept($string){

        // state awal
        $states = [$this->initialState];

        // ubah ke lowercase
        $string = strtolower($string);
        $word = new Word($string);
        while( $word->hasNext() ){

            $symbol = $word->next();
            $new_states = $this->ext_trans_func($states, $symbol);
            $states = $new_states;
        }

        if( $this->isValid($states) ){
            return true;
        }else{
            return false;
        }
    }

    public function ext_trans_func($states, $symbol){

        $union = [];
        foreach ($states as $state){

            if( !isset( $this->transitions[$state][$symbol]) ){
                if( $state == $this->initialState || in_array($state, $this->finalState) ){ // ( $state == $this->initialState || in_array($state, $this->finalState) ) --> text yang mengandung
                    $symbol = "sigma";
                }else{
                    continue;
                }
            }
            $stateDestination = $this->transitions[$state][$symbol];
            $union = array_unique(array_merge($union, $stateDestination));
        }
        return $union;

    }

    public function isValid($states){

        $result = array_intersect($states, $this->finalState);
        if( count($result) > 0 ){
            return true;
        }else{
            return false;
        }

    }

    public static function create_textSearch_machine($string){

        $head = [];
        $transitions = [];
        $finalState = [];
        $initialState = 0;
        $stateDestination = 1;
        $sigma = [];

        // 1.Ambil string yang diinputkan.
        // 2.Pecah menjadi beberapa keywords.
        $keywords = explode(" ", $string);

        foreach ($keywords as $keyword){

            // 3.Ambil per keyword pecah menjadi simbol.
            $keyword = strtolower($keyword);
            $symbols = str_split($keyword);

            // ambil simbol yang terlibat
            $sigma = array_unique(array_merge($sigma, $symbols));

            foreach ($symbols as $position => $symbol){

                $stateBegin = $stateDestination - 1;

                // 4.Buat transisi dalam bentuk array 
                // 5.Ambil karakter pertama dari keyword, simpan ke array head.
                if( $position == 0 ){

                    if( array_key_exists($symbol, $head) ){
                        $head[$symbol][] = $stateDestination;
                    }else{
                        $head[$symbol] = [$stateDestination];
                    }
                    
                }else{
                    $transitions[$stateBegin] = [ $symbol => [$stateDestination] ];
                }

                if( $position + 1 == count($symbols) ){
                    $finalState[] = $stateDestination;
                    $transitions[$stateBegin] = [ $symbol => [$stateDestination] ];

                    //trap
                    // text yang mengandung
                    $transitions[$stateDestination] = [ "sigma" => [$stateDestination] ];

                    // text wajib 1 kata (salah)
                    // $transitions[$stateDestination] = [];
                }

                $stateDestination++;
            }

        }

        // 6.Ulangi step 3 – 5 sampai keywords habis.
        /* 7.Tambahkan transisi initial state yaitu, 
        sigma yang mengarah ke initial state itu sendiri, 
        kemudian simbol – simbol yang disimpan dalam array head. */
        $transitions[$initialState] = ["sigma" => [0]];
        $transitions[$initialState] = array_merge($transitions[$initialState], $head);
        array_unshift($transitions, $transitions[$initialState]);
        array_pop($transitions);

        $allState = array_keys($transitions);
        
        return new NFA($initialState, $finalState, $transitions, $allState, $sigma);
    }

}