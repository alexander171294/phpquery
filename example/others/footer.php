<?php

// this function is execute after controllers.
// this DON'T EXECUTE IF YOU CALL REDIRECT IN CONTROLLER
_::attach_footer(function(){
        _::$view->show('footer');
    });