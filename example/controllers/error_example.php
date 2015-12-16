<?php

// put this line if you use the userErrorHandler in all controllers in this file
//_::declare_component('userErrorHandler');

_::define_controller('ajax_example', function(){
	
	// you need load userErrorHandler component, the framework don't load by default this component, you load it if you use.
	_::declare_component('userErrorHandler');
	// you can put declaration line in index.php if you use the component in all controllers, or out of define_controller if you use in all controllers of this file.
	
	
	// first make a erros using a key word
	_e::set('KEY_WORD', 'this is a example');
	
	// now when you need error, use get function:
	try {
		
		// This is an error
		if(true) throw new Exception(_e::get('KEY_WORD'));
		// use _e::get('KEY_WORD'); to get json of error
		
	} catch(Exception $error)
	{
		// now, show the standarized error json.
		_::$view->ajax_plain($error->getMessage());
	}
	
	// the json error, have this structure
	/*
	 
	 {
	 	code: 000,
	 	key: "KEY_WORD",
	 	message: "message of error"
	 }
	 
	 code is number automatically generated.
	 */
	
	// if you like load other lenguaje use a json in this format:
	// {KEY_WORD: 'en otro idioma', KEY_WORD_OTHER: 'in other language'}
	// and send location of file to function loadLanguage('FILE.json');
	// _e::loadLanguage('FILE.json');
	// then automatically translate all errors.
	
});