<?php

if(isset($_POST['url'])){
    $ch = curl_init();
    $url = $_POST['url'];
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_ENCODING, ""); //Allow to accept any encoding
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $html = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($html === FALSE) {
        echo 'bad_url';
    } else {
        if ($httpcode === 200) {
            libxml_use_internal_errors(true); //Disable errors
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $body = $dom->getElementsByTagName("body");
            if ($body->length != 0) {
                $body = $body->item(0);
            } else {
                $main = $dom->getElementsByTagName("main");
                if ($main->length != 0) {
                    $body = $body->item(0);
                } else {
                    $body = 'no_tags';
                }
            }
            if($body !== 'no_tags'){
                //Delete <a> with content
                $delete_a = $body->getElementsByTagName("a");
                while ($delete_a->length > 0) {
                    $a = $delete_a->item(0);
                    $a->parentNode->removeChild($a);
                }
                //Delete <script> with content
                $delete_script = $body->getElementsByTagName("script");
                while ($delete_script->length > 0) {
                    $script = $delete_script->item(0);
                    $script->parentNode->removeChild($script);
                }
                //Delete <style> with content
                $delete_style = $body->getElementsByTagName("style");
                while ($delete_style->length > 0) {
                    $style = $delete_style->item(0);
                    $style->parentNode->removeChild($style);
                }
                //Get plaintext
                $plainText = $body->textContent;
            } else {
                $plainText = 'no_tags';
            }
            echo json_encode($plainText);
        } else {
            echo 'bad_response';
        }
    }
} else {
    echo 'no_url';
}