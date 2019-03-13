<?php namespace Lingu\System;

    abstract class Controller
    {
        private $_obj = [];
        private $_arr = [];

        public final function __construct(Application $app)
        {
            $this->_obj['Lingu\\System\\Application'] = $app;

            $this->_outputText('Hai, Im Lingu :)', 200);
        }

        public final function _model(&$model)
        {
            $model = 'Lingu\\Model\\'.$name = ucfirst($model);

            if(!isset($this->_obj[$model]))
            {
                if(class_exists($model))
                {
                    if('Lingu\\System\\Model' == get_parent_class($model))
                    {
                        $this->_obj[$model] = new $model($this);
                    }

                    else throw new Exception('model "'.$name.'" tidak sah, class "'.$model.'" bukan anak dari class "Lingu\\System\\Model"');
                }

                else throw new Exception('model "'.$name.'" tidak ditemukan, class "'.$model.'" tidak ada');
            }

            $model = $this->_obj[$model];

            return $this;
        }

        public final function _config(&$config, bool $ncache = false)
        {
            if(null === $config = $this->_arr['config'][$name = $config])
            {
                $this->_obj['Lingu\\System\\Application']
                     ->getRootPath($file);

                if(file_exists($file = $file.'/Config'.'/'.$name.'.ini'))
                {
                    $config  = parse_ini_file($file, true);

                    if(!$ncache)
                    {
                        $this->_arr['config'][$name] = $config;
                    }
                }

                else throw new Exception('config "'.$name.'" tidak ditemukan, file "'.$file.'" tidak ada');
            }

            return $this;
        }

        public final function _injected(&$var)
        {
            $this->_obj['Lingu\\System\\Application']
                 ->getInjected($var);

            return $this;            
        }

        protected final function _outputSet(string $string, int $code, string $mime)
        {
            $this->_obj['Lingu\\System\\Application']
                 ->setOutput($string, $code, $mime);

            return $this;
        }

        protected final function _outputMime(string $mime)
        {
            return $this->_obj['Lingu\\System\\Application']
                        ->setOutputMime($mime);
        }

        protected final function _outputCode(int $code)
        {
            return $this->_obj['Lingu\\System\\Application']
                        ->setOutputCode($code);
        }

        protected final function _outputText(string $string, int $code)
        {
            return $this->_outputSet($string, $code, 'text/plain');
        }

        protected final function _outputHtml(string $string, int $code)
        {
            return $this->_outputSet($string, $code, 'text/html');
        }

        protected final function _outputJson($data, int $code)
        {
            $string = json_encode($data);

            return $this->_outputSet($string, $code, 'application/json');
        }
    }