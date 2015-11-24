1º add autoload to models. (make easy load models)
* now isn't necessary write _::declare_model(), is autodeclared by spl_autoload_class (PHP CORE)

2º improve load_requires function. (improve speed)
* now, isn't load always the components, now loads by demand

3º improve error system. (make easy debug the code)
* now, the errors system equipped with a bugtrace, errors levels, and is possible you set custom error function to catch errors.

4º add control resource consumption.
* now, you can add third parameter to define_controller and second parameter to define_autocall, is boolean, if they value is true, show seconds and memory used to execute the controller or autocall function.
* now, you can put var_dump to _::get_cost() in the end of file index for see memory usage, and seconds of execution of all code. 
Why? you test, evaluate, and improve each function in your script to get the best performance.

5º split examples.
* now, the examples are commented and splitted

6º delete functions.php, this file is not necessary


10º replace attach_header for define_autocall
* now, attach_header not use a stack for load, is changed for _::define_autocall()
[!] for backward compatibility to older versions of PHPQuery, attach_header work, but is an alias of _::define_autocall() (you don't have problem with compatibility)
[!] we recommend you change attach header for define_autocall if it can. Why? we delete in the future this function for reduce resource cost.

[!!!] WE RECOMMEND YOU CHANGE ALL DEPRECATED FUNCTIONS IF IT CAN, WE DELETE IN THE FUTURE ALL DEPRECATED FUNCTIONS FOR REDUCE RESOURCE COST!

OTHERS CHANGES:

* fix bug in ORM