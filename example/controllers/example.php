<?php

// First I declare extra functions:
_::declare_extra('functions'); // this load /extras/functions.php

/**
 * Now I need execute some lines of code, ever!
 * then I define autocall function, this function is it called in the definition
 * that is to say this function called always when example.php id loaded
 */
_::define_autocall(function(){
        
        execute_one_function(); // this is a example function
    
    });

/**
 * in this time, I define a controller:
 * I can define one or more controllers in one file.
 * We recommends depending of case, if you need low cost application (talking about resources), you define one controller per file
 * If you need sort the code, you can define more controllers in one file.
 */

_::define_controller('example', function(){
    
        // my code here...
        
    });

// define other controller
// in this controller is exampled the use of models
_::define_controller('example_2', function(){
    
        // my code here...
        // declare need model example
        // this is not necessary since version 1.0.1
        _::declare_model('example');
        
        // making new record void
        $record = new example();
        
        // put value in the field of record
        $record->field_example = 'value of example';
        
        // in this case, insert new record in the table example
        // and return id of the new record
        $lastInsertID = $record->save();
        
        // in this case, use id for load record
        $record = new example($lastInsertID);
        
        // this show string(16) "value of example"
        var_dump($record->field_example);
        
        // set new value
        $record->field_example = 'new value';
        
        var_dump($record->field_example); // now this show new value
        
        // in this case, this work as "UPLOAD" query.
        $record->save();
        
        var_dump($record->field_example); // this show new value
        
        // this assign the value of record to var $value
        _::$view->assign('value', $record->field_example); // in the html, you able to use {$value} to show the value of $record
        
        // this request use example.tpl as html to return.
        _::$view->show('example');
        
        // this delete the record
        $record->delete();
        
        // this contain the last query executed by the ORM
        // you use this to debug in case of error
        $record->lastQuery;
        
        // this contain the last error in the last query executed by the orm
        $record->lastError;
    });

    // in this controller is exampled use of $_POST $_GET $_SESSION $_COOKIE vars
_::define_controller('example_3', function(){
    
        // if is set example parameter of type post
        if(isset(_::$post['example']))
        {
            // this is a object of postVar class
            $object = _::$post['example'];
            
            // this show the string filtered (avoiding XSRF vulnerability)
            var_dump((string)$object);
            // if you don't like filter you use real() function to transform in original value.
            // you can transform text in UPPER or LOWER
            $object->real()->upper()->lower();
            // in this case show original value without filter, but in lower (first transformed to upper, later to lower)
            var_dump((string)$object);
            
            // others functions:
            $object
                ->real() // real value without filters
                ->noParseBR() // this no transform \r\n to <br />
                ->parseBBC() // transform <br /> to [br]
                ->upper()
                ->lower()
                ->urldecode()
                ->urlencode()
                ->b64_d() // base64 decode
                ->b64_e() // base64 encode
                ->hash() // password hash
                ->md5()
                ->seo(); // to replace all non alphabetic (or numbers) characters to -
            
            // and functions return values:
            
            var_dump($object->len()); // return strlen(); of value
            var_dump($object->check('HASH IN DB')); // return bool value and check if the value match with the hash in db (password_hash and password_verify)
            var_dump($object->isEmail()); // return true if the value is a valid email
            var_dump($object->int()); // return int casted value. (int) $_POST['example'];
            
            // all of this functions works in postVar, getVar, requestVar, sessionVar, cookieVar, are defined in objectVar class
            
            // the property _::$post have an array of objects postVar
            // the property _::$get have an array of objects getVar
            // the property _::$request have an array of object requestVar
            
            // all of this object have each functions.
            // and all able to use directly:
            
            $example = new postVar('example');
            // but if you make new object for post, get, request, session, cookie vars, you making duplicate objects.
            // we strongly recommend don't make more objects, is a lost of resources innecesary, use _::$post property.
            
            // now, the others objects, add new options:
            
            // this is a array of session vars
            _::$session;
            
            $object = _::$session['example'];
            // this have functions of getVar, postVar, etc.
            // and new functions:
            
            $object->destroy(); // unset session['example']
            $object->set('new value'); // session['example'] = 'new value';
            
            // if isn't exist and you need make new use new sessionVar('name');
            if(!isset(_::$session['example']))
            {
                $object = new sessionVar('example');
                $object->set('the value');
            }
            
            // cookie class work equal to session but it has an exception,
            // set function add second parametter to set lifetime of the cookie:
            
            if(!isset(_::$cookie['example']))
            {
                $object = new cookieVar('example');
                $lifetime = new _date();
                $object->set('the value', $lifetime->hours(10));
            }
            
            // to set lifetime you need use TIMESTAMP generated by _date class
            
            $example = new _date();
            
            
            
        }
    
    });