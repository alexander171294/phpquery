2º improve load_requires function. (improve speed)
3º improve error system. (make easy debug the code)
4º add control resource consumption.
5º add documentation
6º delete functions.php, this file is not necessary
7º Check backward compatibility with older version of the framework.
8º improve _date to support initial value and add clear format.
9º debug view class.

-- Control Resource Consumption:
This idea proposes add parameter to define_controller function, allowing dump the consumption of the controller, for debug reasons

-- New error system:
Using a config template (with DB config, and TPL config) set you functions for error control.
