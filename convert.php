<?php

require('src/FastText.php');

$rating_count = [
    1 => 0,
    2 => 0,
    3 => 0,
    4 => 0,
    5 => 0,
    6 => 0,
];


$valid_dirs = ['train', 'dev', 'test'];
if ( empty($argv[1]) || array_search($argv[1], $valid_dirs) === false )
{
    die("Please specify which directory to convert. [dev,test of train]\n");
}
$set = $argv[1];
$fasttext = new FastText("norec_{$set}.txt");
$fasttext->uniqueTextsOnly();

$path = "conllu/$set/";
$d = dir($path);
echo "Path: " . $d->path . "\n";
$total_count = 0;
while (false !== ($file = $d->read()))
{
    if ( pathinfo($file, PATHINFO_EXTENSION) == 'conllu')
    {
        //echo $file."\n";
        $total_count++;
        processFile( $path . $file );

        if ( $total_count % 100 == 0 ) 
            echo " {$total_count}\n";
    }
}
$d->close();

print_r( $rating_count );


function processFile( $file )
{
    global $rating_count, $fasttext;
    $content = file_get_contents($file);
    preg_match_all("/^# ([a-z _]+) = (.+)$/miu", $content, $m);

    $rating = 0;
    $title = '';
    $language = '';
    $text = '';
    
    for( $x =0; $x < count($m[0]); $x++ )
    {
        $key = trim($m[1][$x]);
        $value = trim( $m[2][$x] );

        if ( $key == 'text' )
        {
            if ( empty($title) )
            {
                $title = $value;
                $text .= $value . ".\n";
            }
                $text .= $value . "\n";
        }
        elseif ( $key == 'language' )
            $language = $value;
        elseif ( $key == 'rating' )
            $rating = intval($value);
    }

    $rating_count[$rating]++;
    //echo "TEXT: {$text}\n";
    //echo "TITLE [{$language}]: {$title}\n";
    //echo "LANGUAGE: {$language}\n";
    //echo " --> rating: {$rating}\n";

    $fasttext->save($rating, $text);
}


function cleanText( $text )
{
    $text = mb_strtolower($text);
    $text = str_replace(
            ['!', '?', ',', '.', '»', '–','«', '(', ')', '[', ']', '/', '\\', '+', '£', '$', '&', '=', ':', ';'],  // '-',
            ' ', 
            $text);

    $text = mb_eregi_replace('\\s+', " ", $text);

    return trim( $text );
}