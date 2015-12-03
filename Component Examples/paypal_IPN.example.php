<?php

_::define_controller('ipn_request', function(){
        
        _::declare_component('ipn');
        
        $data = new ipn();
        
        $data->transaction_id;
        $data->item_name;
        $data->payment_status;
        $data->amount;
        $data->currency;
        $data->receiver_email;
        $data->client_email;
    
    }, true);