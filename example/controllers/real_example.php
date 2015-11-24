<?php

// extras are loadeds since extras folder.
_::declare_extra('funciones');

// this function is execute automatically when this file is load
_::define_autocall(function(){
                // require a model
                // ATENTION: SINCE 1.0.1 VERSION, THIS IS NOT NECESSARY, USE AUTOLOAD, BUT THIS WORK ANYWAY FOR BACKWARD COMPATIBILITY
                _::declare_model('usuarios');
                // declare other model:
                _::declare_model('planes');
                // call user defined function.
                basic_headers();
        });


// this define a controller home
// is called by url param, you see index.php for more info.
_::define_controller('home', function(){
        
        // checkLogin is user defined function.
        // _::redirect is a framework function to make a internal redirection (without header(location))
        // the first param is the name of te controller to be called.
        // you able to add second param to force redirect using header('LOCATION ');, but in this case, you need use url in the first param.
        if(!checkLogin()) { _::redirect('login'); return false; }
        
        // this call to function show of the TPL manager, in this case using the default template manager
        // and like include index.tpl
        _::$view->show('index');
        
    });

// define a login controller (for example).
_::define_controller('login', function(){

                define('EXTERA_PAGE', true);
                
                // assign variable "error" to view, and the value void ''
                _::$view->assign('error', '');
                _::$view->show('login'); 
        });

        
_::define_controller('login2', function(){
                define('EXTERA_PAGE', true);
                try{
                        // using $post parsed
                        // this return an object of type postVar
                        $nick = _::$post['user'];
                        $pass = _::$post['password'];
                        // using len function of postVar
                        if($nick->len()<3) throw new exception('Nick invalido');
                        // existis is a static function of usuarios model.
                        $id = usuarios::exists((string)$nick);
                        
                        if($id === false) throw new exception('No existe un usuario con este nick');
                        
                        // this make a record of usuarios table, using $id to specificate value of primary key
                        // this like as 'SELECT * FROM usuarios WHERE primary_key = $id
                        // $id automatically filtered.
                        // after execute query, the ORM class dump results in the properties of the object
                        $usuario = new usuarios($id);
                        
                        // the object $usuario have a property for each field of the table usuarios.
                        
                        if(!$pass->check($usuario->pass_usuario)) throw new exception('La contrase&ntilde;a no es valida');
                        
                        // this construct an object of $_SESSION var
                        $session = new sessionVar('loggued');
                        // change the value to true
                        $session->set(true);
                        // make other object of $_SESSION but this time for $_SESSION['token']
                        $token = new sessionVar('token');
                        // call to user defined function
                        $ht = csrf_token();
                        // change value to $_SESSION
                        $token->set($ht);
                        
                        
                        $idSess = new sessionVar('id');
                        $idSess->set($id);
                        
                        // tenemos un plan asignado?
                        if($usuario->plan_usuario>0)
                        {
                                // class _date is from the framework.
                                $date = new _date();
                                
                                if($usuario->fecha_pago_usuario >= $date->count())
                                {
                                        // redirect using force header('location: '.$param1);
                                        _::redirect('/'.$ht.'/home.html', false);
                                } else { // sin pagar
                                        _::redirect('/'.$ht.'/pagar.html', false);
                                }
                        } else _::redirect('/'.$ht.'/new_plan.html', false);
                } catch(exception $e)
                {
                        _::$view->assign('error', $e->getMessage());
                        _::$view->show('login');
                }
        });

_::define_controller('registro', function(){
                define('EXTERA_PAGE', true);
                _::$view->assign('error', false);
                _::$view->show('registro'); 
        });

_::define_controller('registro2', function(){ // todo: codigo promocional; seleccion de servidor
                define('EXTERA_PAGE', true);
                $nick = _::$post['username'];
                $email = _::$post['email'];
                $pass = _::$post['password'];
                $email = _::$post['email'];
                $pass2 = _::$post['passwordConfirm'];
                $error = null;
                
                // chequeamos que el nick sea razonable
                try
                {
                        if($nick->len() < 3 || $nick->len()>32) throw new exception('nick no valido, al menos 3 y menos de 32 caracteres');
                        if(!$email->isEmail()) throw new exception('email no valido');
                        if($pass->len() < 8) throw new exception('La pass debe tener al menos 8 caracteres');
                        if((string)$pass !== (string)$pass2) throw new exception('Las pass no coinciden');
                
                        // bueno esta todo ok asique revisamos si existe alguien con este nick
                        if(usuarios::exists((string)$nick)) throw new exception('ya existe un usuario con este nick');
                        
                        $user = new usuarios();
                        $user->nick_usuario = (string)$nick;
                        $user->pass_usuario = (string)$pass->hash();
                        $user->plan_usuario = 0;
                        // por cuanto tiempo?
                        $fecha = new _date();
                        $user->fecha_pago_usuario = $fecha->count();
                        
                        // si hay código de promoción
                        if(isset(_::$post['codigopromocional']))
                        {
                                // comprobamos si existe el código
                                
                                // evaluamos qué da
                                $user->plan_usuario = 1;
                                
                                // dentro de 99 años, 11 meses
                                $user->fecha_pago_usuario = $fecha->years(99)->months(11)->count();
                        }
                        
                        if(isset(_::$post['referido']))
                        {
                                $idref = usuarios::exists((string)_::$post['referido']);
                                if($idref)
                                {
                                        $user->referido = $idref;
                                } else throw new Exception('el usuario referido no existe');
                        } else $user->referido = 0;
                        
                        $user->email_paypal = (string)$email;
                        
                        // DESIGNAMOS SERVIDOR
                        $user->server_asignado = 1;
                        
                        $user->fondos_usuario = 0;
                        
                        $user->cuenta_activa = 1;
                        
                        // THIS SAVE THE NEW DATA IN THE OBJECT, DUMPING ALL DATA IN TABLE
                        // like as INSERT or UPDATE depending if the object construct using parametter to specify primary key or not
                        $user->save();

                        // hay que enviar mail aquí
                        
                        _::$view->show('registro_ok');
                        
                } catch(Exception $e) {
                        _::$view->assign('error', $e->getMessage());
                        _::$view->show('registro');
                }
        });
