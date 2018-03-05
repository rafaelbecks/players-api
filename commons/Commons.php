<?php 

class Commons
{
    public static function response($status,$data,$app)
    {
        $app->response->setStatusCode($status);
        $app->response->setJsonContent($data);
        $app->response->send();
    }

    public static function base64ToImg($base64_string, $output_file){
        $ifp = fopen($output_file, "w"); 

        if(strpos($base64_string, ",") === false)
        {
            $base64_string = ",".$base64_string;
        }

        $data = explode(',', $base64_string);
        fwrite($ifp, base64_decode($data[1])); 
        chmod($output_file, 0777); 
        fclose($ifp); 
        return $output_file; 
    }

    /*
    
    Uso de filtros en API

    Los filtros son parámetros GET, para especificar uno solo hace falta agregar a la URL el filtro deseado

    GET api/entidad?campo=valor&otroCampo=otroValor

    Por defecto el operador es el de igualdad, si se desea cambiar el operador debe seguirse la siguiente sintaxis

    Mayor, menor, mayor que, menor que, entre (<>)
    
    GET api/entidad?campo=(<)valor&otroCampo=(>=)otroValor&tercerCampo=(<>)primerValor|segundoValor

    Con estos parámetros se construye la consulta SQL
    
    */

    public static function filterValidator($array){
        $sql = "";
        unset($array["_url"]);
        $queryLogicOperator = "AND";
        if(count($array)>0)
        {
            if(isset($array["or"]))
            {
                $queryLogicOperator = "OR";
                unset($array["or"]);
            }

            foreach ($array as $key => $value) {
                $where = Commons::operatorValidator($value);
                $sql.=$key." ".$where["operator"]." '".$where["value"]."' ".$queryLogicOperator." ";
            }
            return " WHERE ". substr($sql,0,strlen($sql)-4);    
        }else
        {
            return "";
        }
    }    

    private function operatorValidator($value)
    {

        $curatedValue = substr($value,strpos($value,")")+1);

        if(strrpos($value,"(")===false)
        {
            $operator = "=";
            $curatedValue = $value;
        }else
        {
            $operator = str_replace(")","",substr($value,1,strrpos($value,")")));

            if($operator == "<>")
            {
                $values = explode("|",$curatedValue);
                $operator = "BETWEEN";
                $curatedValue = $values[0]." AND ".$values[1];
            }
        }

        $where = [
            "operator" => $operator,
            "value" => $curatedValue
        ];

        return $where;
    }

    public static function getDI()
    {

        $di = new \Phalcon\DI\FactoryDefault();

        $di->set('db', function(){
            return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                "host" => "127.0.0.1",
                "username" => "root",
                "password" => '09111999',
                "dbname" => "players",
                'charset'     => 'utf8'
                ));
        });    

        return $di;
    }


    public static function inArrayContains($array,$search)
    {
        if(count($array)>0 && isset($search)) {
            foreach ($array as $item) 
            {
                if (strpos($search,$item) != false) 
                {
                    return true;
                }
            }
            return false;
        }
    }


}

?>