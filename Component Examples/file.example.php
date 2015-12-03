<?php

_::define_controller('download', function(){
                
                _::declare_component('file');
                
                // force download uploads/file.php
                $file = new file('uploads/', 'file.php');
                $file->download();
            
        });

_::define_controller('upload', function(){
                
                _::declare_component('file');
                
                // for upload:
                $file = new file();
                
                $location = $file->upload('FIELD', 'uploads/', true, true, array('jpg', 'jpeg', 'png', 'gif'));
        
        });