<?php

_::declare_extra('funciones');

// las cosas que tenemos que declarar siempre
_::define_autocall(function(){
                _::declare_model('usuarios');
                _::declare_model('planes');
                
                basic_headers();
        });


// definimos el controlador 'home'
_::define_controller('home', function(){
        
        if(!checkLogin()) { _::redirect('login'); return false; }
        
        _::$view->show('index');
        
    });

_::define_controller('login', function(){
                define('EXTERA_PAGE', true);
                _::$view->assign('error', '');
                _::$view->show('login'); 
        });

_::define_controller('login2', function(){
                define('EXTERA_PAGE', true);
                try{
                        $nick = _::$post['user'];
                        $pass = _::$post['password'];
                        if($nick->len()<3) throw new exception('Nick invalido');
                        $id = usuarios::exists((string)$nick);
                        if($id === false) throw new exception('No existe un usuario con este nick');
                        $usuario = new usuarios($id);
                        if(!$pass->check($usuario->pass_usuario)) throw new exception('La contrase&ntilde;a no es valida');
                        
                        // existe
                        $session = new sessionVar('loggued');
                        $session->set(true);
                        $token = new sessionVar('token');
                        $ht = csrf_token();
                        $token->set($ht);
                        $idSess = new sessionVar('id');
                        $idSess->set($id);
                        
                        // tenemos un plan asignado?
                        if($usuario->plan_usuario>0)
                        {
                                // si estamos pagados
                                $date = new _date();
                                if($usuario->fecha_pago_usuario >= $date->count())
                                {
                                        // estamos al día
                                        _::redirect('/'.$ht.'/home.html', false);
                                } else { // sin pagar
                                        // tratamos de debitar del dinero interno
                                        
                                        // si no podemos
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
                        
                        $user->save();

                        // hay que enviar mail aquí
                        
                        
                        _::$view->show('registro_ok');
                        
                } catch(Exception $e) {
                        _::$view->assign('error', $e->getMessage());
                        _::$view->show('registro');
                }
        });

_::define_controller('recordar_pass', function(){
                define('EXTERA_PAGE', true);
                _::$view->show('recordar');
        });