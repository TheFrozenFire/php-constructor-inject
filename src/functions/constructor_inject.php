<?php
/**
 * Simplified constructor injection
 * 
 * PHP does not currently have a way to inject parameters to constructors
 * directly into their respective properties. This leads to a lot of boilerplate
 * code for assigning each parameter to each property. This function eliminates
 * that by performing those assignments automatically via reflection.
 *
 * @param object $instance - Pass $this, always.
 * @param array $args - Pass func_get_args(), almost always
 * @param array $exclude = [] - An array of either positional parameter or named parameters to exclude
 * @param string $classContext = null - If this class might be extended, pass self::class
 * @return void
 */
function constructor_inject($instance, $args, $exclude = [], $classContext = null)
{
    $classContext = $classContext?:$instance;

    $reflection = new ReflectionClass($classContext);
    $constructor = $reflection->getConstructor();
    $params = $constructor->getParameters();
    
    foreach($args as $pos => $value) {
        if(in_array($pos, $exclude, true)) {
            continue;
        }
        
        $param = $params[$pos];
        $paramName = $param->getName();
        if(in_array($paramName, $exclude, true)) {
            continue;
        }
        
        $prop = $reflection->getProperty($paramName);
        $prop->setAccessible(true);
        $prop->setValue($instance, $value);
    }
}
