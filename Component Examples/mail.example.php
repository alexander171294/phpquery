<?php

_::define_controller('mail', function(){
        
        // simple mail, not SMTP!
        _::declare_component('mailer');
        
        $html = 'hello <b>world</b>, go to mi site please <a href="http://google.com">mi site</a>';
        
        $mail = new mailer('admin@site.com', 'user@gmail.com', 'testing mailer', $html);
        $result = $mail->send();
        
        if(!$result) die($mail->error_message);
    
    }, true);