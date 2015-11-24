PHPQuery

**¿Por qué lo desarrollamos?**
No existe absolutamente ningún framework más ligero, robusto, seguro, y flexible que PHPQuery, lo desarrollamos porque simplemente lo necesitamos.

**¿Por qué usar PHPQuery?**, PHPQuery está inspirado en jquery uno de los frameworks más simples que existen, de ahí su nombre, únicamente tiene como objetivo el hecho de reconocer la inspiración sobre JQuery. La razón por la que debes, necesitas, conocer y utilizar phpquery es por sus objetivos que se mencionarán a continuación.

**¿Cuáles son los objetivos principales de PHPQuery?**
1º Sumamente simple
2º Sumamente rápido de implementar
3º Híper-Fast
4º Híper Ligero
5º Alta seguridad.
1º hacer una aplicación se basa en 3 partes, modelo, vista y controlador.

Con PHPQuery los controladores son realmente sencillos, basta con poner _::define_controller(‘Nombre’, function(){ CODIGO DE MI CONTROLADOR }); para tener un nuevo controlador listo para utilizar.
PHPQuery incluye un ligero pero muy útil ORM que permite simplemente crear modelos basados en tu tabla, por ejemplo si quieres tener un modelo de la tabla usuarios solo tienes que poner class usuarios{ public $campo1; public $campo2; protected $primaryKeys = ‘Claves primarias de mi tabla’; } y ya tienes hecho el modelo, luego el mismo cuando se cree una instancia obtendrá todos los datos, si lo cambias guardará todos los datos, y etc. Pero sin que escribas una línea de código.
Con PHPQuery las vistas son muy simples, solo debes usar {$variables} {#constantes#} {loop=$bucles as $clave to $valor} {if=”micondicion”}, no utilizamos un gran gestor de plantillas es muy muy simple y ligero, lo que lo hace ULTRA rápido, consta de solo 140 lineas de código (contando líneas vacías ;). 

2º Al tener modelos muy simples que no requieren ni una sola línea de SQL, para realizar las Altas Bajas y Modificaciones, no hay que crear grandes estructuras para los controladores y las vistas son super simples, aprender a utilizar el framework y escribir código en él, resulta realmente rápido.

3º Cuando decimos híper rápido nos referimos a que PHPQuery tiene un core muy simple y no carga absolutamente nada que no sea necesario, y además carga las cosas en el momento en que serán usadas, esto hace que el acceso a los controladores sea rápido sin muchas vueltas, la carga de distintas cosas que se hacen posteriormente no afecta al procesamiento inicial y además se realiza en el momento que se necesita.

4º El hecho de que el framework solo cargue un nucleo muy pequeño, que únicamente cargue 5 clases más chicas para manejar cosas necesarias, y el controlador de DB que oh! Resultan ser solo 3 lineas (se instancia PDO) lo hace realmente barato en cuanto a consumos de recursos por parte del framework, luego respecto a las vistas, son cargadas y cacheadas 1 sola vez (es decir que si no se encuentra  activado el modo desarrollador las vistas son reutilizadas y cacheadas para reducir el impacto del consumo de recursos), luego por parte de los controladores la flexibilidad de PHPQuery permite crear muchos controladores en uno, o muchos controladores en distintos archivos, reduciendo al minimo la carga de controladores (solo se carga el controlador que se precisa, el resto del código no se carga). Por parte de los modelos, todos extienden un grupo ORM que reduce a gran escala la cantidad de consultas que se deben escribir para manejar la db en cualquier otro framework.

5º ¿Por qué alta seguridad? Realmente necesitamos que nuestras aplicaciones sean seguras, y no tengamos que depender de aplicar filtros que nos pueden llevar a olvidarnos alguno y dejar grandes baches en el código. En cuanto a Base de datos utilizamos PDO nativo de PHP que tiene un sistema de filtrado integral implementado nativamente con consultas preparadas. En cuanto a las variables POST, GET, REQUEST incluimos un sistema que filtra automáticamente las variables para evitar problemas. En cuanto a codificación de caracteres incluimos librerías extras (que no se cargan por si solas pero si se puede solicitar su carga) para manipular distintas codificaciones de caracteres. En cuanto a manejo de archivos, de la misma forma, es una librería extra que no se carga a excepción de que se demande, el framework evita SQLI, XSRF, LFI, RFI, y carga de archivos peligrosos automáticamente.

**¿Cuál es el futuro?**
Todos los días mejoramos el CORE de PHPQuery para reducir su consumo (no aumentarlo) separando cada vez más las cosas que se cargan de las cosas que no se cargan hasta ser pedidas. Trabajamos a diario en las últimas medidas de seguridad y en simplificar cada vez más la tarea de programar, tenemos como política que los desarrolladores deben necesitar escribir cada vez menos líneas de código. Tenemos como principal objetivo el desarrollo en equipo, por eso planteamos crear una nueva forma de desarrollar para equipos (aún en fase de diseño).

**¿A qué apuntamos?**
Escalabilidad, apuntamos a que nuestro framework sea útil cuando uno desarrolla, sea útil cuando uno recién comienza, pero sea realmente útil y eficaz cuando uno escala su proyecto a una gran masa de usuarios, cuando realmente el rendimiento es importante, nuestro framework podría considerarse de ultra bajo consumo, ultra ligreo, ultra rápido, y bastante simple de utilizar, además de seguro.
PDO, ORM, PLANTILLAS, MVC son alguna de las cosas que implementamos en nuestro framework, pero no lo implementamos rápidamente, sino que planificamos y diseñamos la mejor forma de implementarlo que tenga el mejor impacto en rendimiento.
Si tú quieres una aplicación que consuma 100 veces menos que Wordpress aunque tenga las mismas prestaciones, pues este framework te ayudará.
Trabajamos a diario con este framework, lo implementamos en ambientes reales, y lo mejoramos hora tras hora buscando la perfección en rendimiento, simpleza, extensibilidad, robustez, flexibilidad y seguridad.
