<?php

_::define_controller('search', function(){
        
        _::declare_component('searcher');
        
        $toSearch = _::$post['q'];
        
        $search = new Buscador((string)$toSearch);
        
        // get querys to execute in db
        $querys = $search->getQuerys();
        
        foreach($querys as $query)
        {
            $pdo = _::$db->prepare('SELECT id FROM posts WHERE title_post LIKE \'%'.$query.'%\'');
            $pdo->execute();
            $results = $pdo->fetchAll();
            // join all results
            $search->merge($results);
        }
        
        $LIMIT = 10; // limit of results
        // delete repeated results
        $results = $search->filterQuerys('id', $LIMIT);
        
        // $search->error (BOOLEAN)
        /* $search->error_id (ONE OF THIS CONSTANTS:
         * BUSCADOR_TEXTO_PEQUENIO, BUSCADOR_PALABRAS_PEQUENIAS, BUSCADOR_MUCHAS_PALABRAS
         * Little TEXT, little words, many words, respectively)
         */
        // now, in $results is array of results
    
    }, true);