<?php namespace Lingu\System;

    final class Application
    {
        private $root       = null;
        private $injected   = null;
        private $running    = false;

        private $output = [];
		private $params	= [];

		private  static $placeholders = [
            'any'      => '.*',
            'segment'  => '[^/]+',
            'alphanum' => '[a-zA-Z0-9]+',
            'num'      => '[0-9]+',
            'alpha'    => '[a-zA-Z]+'
        ];

        public function __construct($inject, string $root = '../lingu')
        {
            $this->setRootPath($root);

            $this->injected = $inject;        
        }

        public function getRootPath(&$root)
        {
            $root = $this->root;
        }

        public function setRootPath(string $root)
        {
            $this->root = realpath($root);
        }

 		public function handle(string $method, string $pattern, string $controller)
		{
		    if($this->running)
		    {
		        throw new Exception('jangan memanggil '.__method__);
		    }

		    if($this->match($method, $pattern))
		    {
		        if(preg_match('|^([a-zA-Z]+)\@([a-zA-Z]+)$|', $controller, $match))
		        {
		            if(class_exists($controller = 'Lingu\\Controller\\'.$match[2]))
		            {
		                if('Lingu\\System\\Controller' == get_parent_class($controller))
		                {
    		                if(method_exists($object = new $controller($this), $match[1]))
    		                {
    		                    $this->running = true;

    		                    ob_start();

    		                    try
    		                    {
        		                    call_user_func([$object, $match[1]], $this->params);

        		                    $ob_content = ob_get_contents();
    		                    }

    		                    catch(\Throwable $error)
    		                    {
    		                        $this->setOutput($error, 500, 'text/plain');
    		                    }

    		                    ob_end_clean();

    		                    if(!empty($ob_content))
    		                    {
    		                        $this->setOutputString($ob_content);
    		                    }
                    
                                exit;
    		                }
    
    		                throw new Exception('controller "'.$match[0].'" tidak ditemukan, method "'.$controller.'::'.$match[1].'" tidak ada');
		                }

		                throw new Exception('controller "'.$match[0].'" tidak sah, class "'.$controller.'" bukan anak dari class "Lingu\\System\\Controller"');
		            }

		            throw new Exception('controller "'.$match[0].'" tidak ditemukan, class "'.$controller.'" tidak ada');
		        }

		        throw new Exception("nama controller '$controller' tidak sesuai pola: 'method@class' dan hanya boleh menggunakan huruf");
		    }
		}

		private function match(string $method, string $pattern): bool
		{
		    if($_SERVER['REQUEST_METHOD'] == strtoupper($method))
		    {
    		    $this->proccessPattern($pattern);
    
    		    if(preg_match('|^'.$pattern.'$|', $_SERVER['REQUEST_URI'], $match))
    		    {
    		        while($key = array_keys($this->params)[intval($i++)])
    		        {
    		            $this->setParam($key, $match[$i]);
    		        }

    		        return true;
    		    }
		    }

		    return false;
		}

        private function proccessPattern(&$arg)
        {
        	if(is_array($arg))
        	{
        		if($arg[2] != "_")
        		{
        			$this->setParam($arg[2]);

        			return "(".self::$placeholders[$arg[1]].")";
        		}

        		return self::$placeholders[$arg[1]];
        	}

        	else $this->params = [];

        	$arg = preg_replace_callback('|\\\\\[([a-zA-Z]+)\\\\\:([a-zA-Z_]+)\\\\\]|', __method__, preg_quote($arg));
        }

        private function setParam(string $key, $value = null)
        {
        	$this->params[$key] = $value;
        }

        public function setOutput(string $string, int $code, string $mime)
        {
            $this->setOutputString($string)
                 ->setOutputCode($code)
                 ->setOutputMime($mime);

            return $this;
        }

        public function setOutputString(string $string)
        {
            $this->output['string'] = $string;

            return $this;
        }

        public function setOutputCode(int $code)
        {
            $this->output['code'] = $code;

            return $this;
        }

        public function setOutputMime(string $mime)
        {
            $this->output['mime'] = $mime;

            return $this;
        }

        public function getInjected(&$var)
        {
            $var = $this->injected;

            return $this;
        }

        public function __destruct()
        {
            if(!$this->output)
            {
                $this->setOutputString('tidak bisa melakukan '.$_SERVER['REQUEST_METHOD'].' pada '.$_SERVER['REQUEST_URI'])
                     ->setOutputMime('text/plain');
    
                if('GET' == $_SERVER['REQUEST_METHOD'])
                {
                    $this->setOutputCode(404);
                }
    
                else
                {
                    $this->setOutputCode(405);
                }
            }

            $statuses = [
                200 => 'OK',
                201 => 'Created',
                204 => 'No Content',
                206 => 'Partial Content',

                301 => 'Moved Permanently',
                302 => 'Found',

                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                409 => 'Conflict',
                413 => 'Payload Too Large',
                415 => 'Unsupported Media Type',
                422 => 'Unprocessable Entity',
                429 => 'Too Many Requests',

                500	=> 'Internal Server Error'
            ];

            header($_SERVER['SERVER_PROTOCOL'].' '.$this->output['code'].' '.$statuses[$this->output['code']]);
            header('Content-Type: '.$this->output['mime']);

            print $this->output['string'];
        }
    }