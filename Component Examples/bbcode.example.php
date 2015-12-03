<?php

_::define_controller('search', function(){
        
        _::declare_component('bbcode');
        
        // first, define list of bbcodes available.
        $lista = array();
        
        $lista[] = bbp_generate_bbcode_simple('hr', '<hr>'); // [hr] -> <hr>
        $lista[] = bbp_generate_bbcode_simple('br', '<br />'); // [br] -> <br />
        
        $lista[] = bbp_generate_bbcode_double('b', '<b>', '</b>'); // [b]?[/b] -> <b>?</b>
        $lista[] = bbp_generate_bbcode_double('s', '<s>', '</s>'); // [b]?[/b] -> <b>?</b>
        
        $lista[] = bbp_generate_bbcode_smiley(':)', '<img src="http://png.findicons.com/files/icons/1577/danish_royalty_free/32/smiley.png">'); // emoticono :)
        
        $lista[] = bbp_generate_bbcode_double_params('url', '<a href="?">', '</a>'); // [url=google.com]example[/url] -> <a href="google.com">example</a>
        
        $example = '[br]Hola, soy [b]alexander[/b][br] esto es una prueba :) [s]FEOS![/s] [br][hr][br] [url=http://basecode.org]BaseCode[/url][br] Voy a probar ahora, que pasa si dejo una etiqueta abierta como [b]esta[br][br]';
        $errors = null;
        
        echo $lista[3]; // mostramos el bbcode [s] para probar que se puede hacer un hecho directamente al objeto
        
        // vemos el texto parseado
        echo bbp_parse($lista, $example, $errors);
        
        var_dump($errors);
    
    }, true);