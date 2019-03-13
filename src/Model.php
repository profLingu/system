<?php namespace Lingu\System;

	abstract class Model
	{
	    private $_controller = null;

	    public final function __construct(Controller $controller)
	    {
	        $this->_controller = $controller;
	    }

	    protected final function _model(String &$model)
	    {
	        try
	        {
    	        $this->_controller
    	             ->_model($model);
    
	        }

	        catch(Exception $e)
	        {
	            throw new Exception($e->getMessage());
	        }
	    }

	    protected final function _config(String &$config, bool $ncache = false)
	    {
	        try
	        {
    	        $this->_controller
    	             ->_config($config, $ncache);
    
	        }

	        catch(Exception $e)
	        {
	            throw new Exception($e->getMessage());
	        }
	    }
	}