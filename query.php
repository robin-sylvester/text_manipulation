<?php

function utf8_strrev($str){
    preg_match_all("/./us", $str, $ar);
    return join("", array_reverse($ar[0]));
}

function create_memcache(){
    $mem  = new Memcached();
    $mem->addServer('web_server_ip_address', 11211);
    return $mem;
}

function permute($str){
    preg_match_all("/./us", $str, $ar);
    shuffle($ar[0]);
    return join("", $ar[0]);
}

function multiexplode ($delimiters,$data) {
    $MakeReady = str_replace($delimiters, $delimiters[0], $data);
    $Return    = explode($delimiters[0], $MakeReady);
    return  $Return;
}

function mb_substr_replace($str, $repl, $start, $length = null)
{
    preg_match_all('/./us', $str, $ar);
    preg_match_all('/./us', $repl, $rar);
    $length = is_int($length) ? $length : utf8_strlen($str);
    array_splice($ar[0], $start, $length, $rar[0]);
    return implode($ar[0]);
}

function reverse_sentence($str){
    $i = 0;
    $result = "";
    $temp = "";
    for($i=0; $i<strlen($str); $i++){
        if($str[$i] == " "){
            $result = " " . $temp . $result;
            $temp = "";
    	}
	    else
            $temp .= $str[$i];
    }
    return $temp . $result;
}

function countSentences($str){
    return preg_match_all('/[^\.\!\?]+/', $str, $match);
}

function finish_transformation($str){
    $mem = create_memcache();
    $mem->set("text", $str);
    header('Location: index.php');
    exit;
}

if (isset($_POST['text'])){
    $mem = create_memcache();
    $mem->set("original", $_POST['text']);
    $mem->set("text", $_POST['text']);
    header('Location: index.php');
    exit;
}
elseif (isset($_POST['result'])){

    switch(true)
    {
        case isset($_POST['first']):
            $result = $_POST['result'];
            preg_match_all('~\w+(?:-\w+)*~', $result, $matches);

            foreach ($matches[0] as $match) {
                if(mb_strlen($match) >= 4)
                    $permuted_word = mb_substr($match, 0, 1) . permute(mb_substr($match, 1, mb_strlen($match)-2)) . mb_substr($match, -1);
                else
                    $permuted_word = $match;
                $pos = strpos($result, $match);
                $result = substr_replace($result, $permuted_word, $pos, mb_strlen($match));
            }

            finish_transformation($result);
        break;
        case isset($_POST['second']):
            $result = $_POST['result'];
            preg_match_all('~\w+(?:-\w+)*~', $result, $matches);

            $sentence_array = multiexplode(array(".", "?", "!"), $result);
            $trimmed_array = array_map('trim', $sentence_array);
            $first_words = array();

            foreach ($trimmed_array as $sentence){
                $arr = explode(' ',trim($sentence));
                if($arr[0])
                    array_push($first_words, $arr[0]);
            }

            foreach ($first_words as $match) {
                $word = lcfirst($match);
                $reversed_word = utf8_strrev($word);
                $reversed_word = ucfirst($reversed_word);

                $pos = strpos($result, $match);
                $result = substr_replace($result, $reversed_word, $pos, strlen($match));
            }

            $leftovers = array_diff($matches[0], $first_words);

            foreach ($leftovers as $match) {
                $reversed_word = utf8_strrev($match);

                $pos = strpos($result, $match);
                $result = substr_replace($result, $reversed_word, $pos, strlen($match));
            }

            finish_transformation($result);
        break;
        case isset($_POST['third']):
            $result = $_POST['result'];

            if (strpos($result, ".") !== false || strpos($result, "?") !== false || strpos($result, "!") !== false) {

                $sentence_array = multiexplode(array(".", "?", "!"), $result);
                foreach($sentence_array as $sentence){
                    if (!empty($sentence))
                    $pos = strpos($result, $sentence);

                    $sentence = lcfirst($sentence);
                    $reversed_sentence = reverse_sentence($sentence);
                    $reversed_sentence = ucfirst($reversed_sentence);

                    $result = substr_replace($result, $reversed_sentence, $pos, strlen($sentence));
                }
            }
            else
                $result = reverse_sentence($result);

            finish_transformation($result);
        break;
        case isset($_POST['fourth']):
            if (strpos($_POST['result'], ".") !== false || strpos($_POST['result'], "?") !== false || strpos($_POST['result'], "!") !== false) {

                $result = $_POST['result'];

                $final = "";
                $temp = "";
                $index = 0;
                for($i=0; $i < strlen($result); $i++){
                    if($result[$i] == "." || $result[$i] == "?" || $result[$i] == "!"){
                        $temp = substr($result, $index, $i - $index + 1);
                        $final = $temp . $final;
                        $index = $i+1;
                    }
                }

                finish_transformation($final);
            }
            else
                finish_transformation($_POST['result']);
        break;
        case isset($_POST['statistics']):
            $result = $_POST['result'];

            preg_match_all('/[aeiou]/i', $result, $matches);
            $count_vowels = sizeof($matches[0]);
            $sentences = countSentences($result);
            $count_a = $count_e = $count_i = $count_o = $count_u = 0;

            foreach($matches[0] as $vowel){
                switch ($vowel) {
                    case "a":
                    case "A":
                        $count_a+=1;
                        break;
                    case "e":
                    case "E":
                        $count_e+=1;
                        break;
                    case "i":
                    case "I":
                        $count_i+=1;
                        break;
                    case "o":
                    case "O":
                        $count_o+=1;
                        break;
                    case "u":
                    case "U":
                        $count_u+=1;
                        break;
                }
            }

            $data = array(
                'sentences' => $sentences,
                'vowels' => $count_vowels,
                'a' => $count_a,
                'e' => $count_e,
                'i' => $count_i,
                'o' => $count_o,
                'u' => $count_u
            );

            $mem = create_memcache();
            $mem->set("text", $_POST['result']);
            header('Location: index.php?' . http_build_query($data));
            exit;
        break;
    }
}
elseif (isset($_GET['flush'])){
    $mem = create_memcache();
    $mem->flush();
    header('Location: index.php');
    exit;
}
else {
    header('Location: index.php');
    exit;
}

?>
