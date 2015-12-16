CHANGELOG OF THE VERSION 1.0.1

o add autoload to models. (make easy load models)
* now isn't necessary write _::declare_model(), is autodeclared by spl_autoload_class (PHP CORE)

o improve load_requires function. (improve speed)
* now, isn't load always the components, now loads by demand

o improve error system. (make easy debug the code)
* now, the errors system equipped with a bugtrace, errors levels, and is possible you set custom error function to catch errors.

o add control resource consumption.
* now, you can add third parameter to define_controller and second parameter to define_autocall, is boolean, if they value is true, show seconds and memory used to execute the controller or autocall function.
* now, you can put var_dump to _::get_cost() in the end of file index for see memory usage, and seconds of execution of all code. 
Why? you test, evaluate, and improve each function in your script to get the best performance.

o split examples.
* now, the examples are commented and splitted

o delete functions.php, this file isn't longer necessary 

o Checked backward compatibility with older version of the framework.

o improve "date" to support initial value and add "clear format" function.
* now you construct object "date" using a unixstamp in replace of time(), use for create "date" of value in db.

o debug view class.
* now you can use {function}, fixed {if} 

o replace "attach header" for "define autocall"
* now, attach_header not use a stack for load, is changed for _::define_autocall()

o Added user error handler
* now, you can make own errors, standarized and translatable

o new component curl added
* simple function to get ssl page using curl

o added component youtubeDownloader
* simple class to get information about video in youtube

o added ajax_plain, and globals vars to view
* now you can call ajax_plain to show plain text, and access to globals vars

[!] for backward compatibility to older versions of PHPQuery, attach_header works, but is an alias of _::define_autocall() (you don't have problem with compatibility)
[!] we recommend you change attach header for define_autocall if it can. Why? we delete in the future this function for reduce resource cost.

[!!!] WE RECOMMEND YOU CHANGE ALL DEPRECATED FUNCTIONS IF IT CAN, WE DELETE IN THE FUTURE ALL DEPRECATED FUNCTIONS FOR REDUCE RESOURCE COST!


-- Control Resource Consumption:
This idea proposes add parameter to define_controller function, allowing dump the consumption of the controller, for debug reasons

-- New error system:
Using a config template (with DB config, and TPL config) set you functions for error control.


OTHERS CHANGES:

* fix bug in ORM
* fix bug in view class
* fix singleton in line 10
* improve view class deleting .h, header.var, and reduce the cost of resources in 32%
* improve performance in views
* Control resource consumption don't show if debug mode is off

BACKWARD COMPATIBILITY:

In this version, we take all possible measures to support backward compatibility, that is, you can replace new PHPQuery version and delete older version, and all works fine... or better