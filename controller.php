<?php


defined('BASEPATH') or die('not found');

class Controller
{
    private $errorMessage = "";
    private $messages = [];

    public function __construct(){
        $this->helper('url');
    }

    public function view($name, $data = [])
    {
        $path =  __DIR__ . '/view/' . strtolower($name) . '.php';
        if(file_exists($path)){
            isset($data) && extract($data);
            require $path;
        }else{
            die('File not found :'.$path);
        }
    }

    public function template($name,$ex = []){

        $data = "";
        $file = __DIR__ .'/view/'.strtolower($name). '.php';
        if(file_exists($file)){
            extract($ex);
            ob_start();
            require $file;
            $data = ob_get_clean();
        }
        return $data;
    }

    public function model($name)
    {
        require __DIR__ . '/model/' . strtolower($name) . '.php';
        return new $name();
    }
    public function library($name){
        require __DIR__. '/libraries/'.strtolower($name) .'.php';
        return new $name();
    }
    public function component($component,$params = [],$response = false){
        if( $response){
            return $this->template('components/'.$component,$params);
        }else{
            if(is_array($params))
                extract($params);
            require __DIR__ .'/view/components/'.strtolower($component). '.php';
        }
    }
    public function abort($code,$message = ''){
        $message = http_response($code,$message);
        $this->view('abort/master',["error_code"=>$code,"error_message"=>$message]);
        exit;
    }
    public function statik($name){
        require __DIR__ .'/view/statik/'.strtolower($name). '.php';
    }

    public function helper($name)
    {
        $file =  __DIR__ . '/helpers/'.strtolower($name).'_helper.php';
        if(file_exists($file))
            require $file;
        else
            die("No exits helper file : ".$name);
    }
   

    private function get_log_path(){
        $FILENAME = "system.txt";
        $FILE = __DIR__ . "/log/" . $FILENAME;
        if(!file_exists($FILE)) file_put_contents($FILE,'');
        return $FILE;
    }
    public function log($type,$message){

       
        $FILE = $this->get_log_path();
        // 11.05.2020 10:11:55
        $time = date("d.m.Y h:i:s",time());
      
        $msg = $time."\t".strtoupper($type)."\t".$message."\n";
        $fp = fopen($FILE, 'a+')  ;
        fwrite($fp, $msg);
        fclose($fp);

    }
    public function GetLogs(){
        $FILE = $this->get_log_path();

        $f = fopen($FILE,'r');
        $data = [];
        $i = 0;
        while(!feof($f)) {
            $str = fgets($f);
            if(!empty($str)){
                 $split = explode("\t",$str);
                 if(count($split) == 3){
                    $data[$i]['date'] = $split[0];
                    $data[$i]['type'] = $split[1];
                    $data[$i]['text'] = $split[2];
                 }
            }
            $i++;
        }
        fclose($f);

        return $data;

    }
  

    public function isPost(){
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }
    public function ClearPost(){
        foreach($_POST as &$item) $item = "";
    }
    
    public function Message($message,$type){
        $this->messages[] = [
            "message"=>$message,
            "type"=>$type
        ];
    }
    public function anyMessage(){
        return count($this->messages) > 0;
    }
    public function getMessages(){
        return $this->messages;
    }
}