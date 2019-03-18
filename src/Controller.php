<?php namespace Lingu\System;

    abstract class Controller
    {
        private $_app = null;

        public final function __construct(Application $app)
        {
            $this->_app = $app;

            $this->_outputText('Hai, Im Lingu :)', 200);
        }

        public final function _injected(&$var)
        {
            $this->_app
                 ->getInjected($var);

            return $this;            
        }

        public final function _model(&$var)
        {
            $this->_app
                 ->model($var);

            return $this;            
        }

        public final function _config(&$var)
        {
            $this->_app
                 ->config($var);

            return $this;            
        }

        protected final function _outputSet(string $string, int $code, string $mime)
        {
            $this->_app
                 ->setOutput($string, $code, $mime);

            return $this;
        }

        protected final function _outputMime(string $mime)
        {
            return $this->_app
                        ->setOutputMime($mime);
        }

        protected final function _outputCode(int $code)
        {
            return $this->_app
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