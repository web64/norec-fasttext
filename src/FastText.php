<?php

class FastText{

    private $filename;
    private $text_max_length = false;
    private $unique_text_only = false;

    public $max_texts_per_label = null;
    public $labels_count = [];
    public $labels_text_length = [];
    public $unique_hash = [];

    function __construct( $filename, $append = false )
    {
        $this->filename = $filename;

        // Clear existing file
        if ( !$append )
            file_put_contents( $this->filename, '');
    }

    public function setTextMaxLength( $text_max_length )
    {
        $this->text_max_length = $text_max_length;
        return $this;
    }

    public function setMaxTextsPerLabel( $max_texts_per_label )
    {
        $this->max_texts_per_label = $max_texts_per_label;
        return $this;
    }

    public function uniqueTextsOnly( $value  = true )
    {
        $this->unique_text_only = (bool)$value;
        return $this;
    }

    public function save( $labels, $text)
    {
        $this->saveLine( 
            $this->getLine( $labels, $text )
         );
    }

    public function getLine( $labels, $text )
    {
        if ( !is_array($labels) )
            $labels = [$labels];

        $text = self::cleanText( $text );
        if ( $this->text_max_length  )
            $text = self::trimText( $text, $this->text_max_length  );


        if ( empty($text) )
            return null;

        if ( $this->unique_text_only )
        {
            $hash = md5($text);

            if ( array_search($hash, $this->unique_hash ) !== false )
            {
                echo " - Ignore duplicate text\n";
                return null;
            }

            $this->unique_hash[] = $hash;
        }

        $label_string = '';
        foreach( $labels as $label )
        {
            if ( !empty($label_string) )
                $label_string .= ' ';

            if ( $this->max_texts_per_label && isset($this->labels_count[$label]) && $this->labels_count[$label] >= $this->max_texts_per_label )
            {
                echo "Max Docs reached for {$label} - {$this->labels_count[$label]} \n";
                break;
            }

            $label_string .= '__label__' . $label;

            if ( !isset($this->labels_count[$label]) )
            {
                $this->labels_count[$label] = 1;
                $this->labels_text_length[$label] = mb_strlen( $text );
            }
            else{
                $this->labels_count[$label]++;
                $this->labels_text_length[$label] += mb_strlen( $text );
            }
        }

        if ( empty($label_string) ) 
            return null;

        return $label_string . ' '. $text;
    }

    public function saveLine( $line )
    {
        if (empty($line)) return;

        file_put_contents( $this->filename, $line . PHP_EOL, FILE_APPEND);
    }

    public static function cleanText( $text )
    {
        $text = mb_strtolower($text);
        $text = str_replace(
                ['!', '?', ',', '.', '»', '–','«', '(', ')', '[', ']', '/', '\\', '+', '£', '$', '&', '=', ':', ';', '”'],  // '-',
                ' ', 
                $text);

        $text = mb_eregi_replace('\\s+', " ", $text);

        return trim( $text );
    }


    public static function trimText($text, $length)
    {
        if ( mb_strlen($text) <= $length) {
            return $text;
        }
      
        return mb_substr(
                $text, 
                0, 
                mb_strrpos(mb_substr($text, 0, $length), ' ')
        );
    }
}