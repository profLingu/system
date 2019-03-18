<?php namespace Lingu\System;

	abstract class Model
	{
	    private $_app = null;

	    public final function __construct(Application $app)
	    {
	        $this->_app = $app;
	    }

	    protected final function _model(String &$model)
	    {
	        try
	        {
    	        $this->_app
    	             ->model($model);
    
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
    	        $this->_app
    	             ->config($config, $ncache);
    
	        }

	        catch(Exception $e)
	        {
	            throw new Exception($e->getMessage());
	        }
	    }
	}