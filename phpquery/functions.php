<?php

function load_component($var)
{
    return require('components/'.$var.'.php');
}