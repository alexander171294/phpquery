<html>
    <body>
        <!-- YOU HTML HERE -->
        {$example} <!-- {$example} is a variable set by _::$view->assign('variable', 'value'); in controller -->
        
        {if="CONDITION HERE $variable"}
            <!-- html here -->
        {/if}
        
        {loop $variables as $var}
            <!-- this like a foreach($variable as $var){
            
            } -->
        {/loop}
        
        {loop $variables as $key to $var}
            <!-- this like a foreach($variable as $key => $var){
            
            } -->
        {/loop}
        
        {#CONSTANT#}
        
        {$example['index']}
        {$example->property}
        {$example['index']->property}
        
        {dump=$variable} <!-- equal to var_dump($variable); -->
        
        {url} <!-- autodetect link example: http://localhost -->
        {url:full} <!-- autodetect full link example: http://localhost/index.php?action=example !-->
        
        {function="example($var01)"}
        <!-- for now isn't possible use next codes, I expect in the future you are allowed to use it

            {include="other"} // load another tpl,
            but i've doubt about this, in theory, you don't can load anothers tpl here, you need load in controller.
            The tags are to show the data in view not for load another view
            
        -->
        
    </body>
</html>