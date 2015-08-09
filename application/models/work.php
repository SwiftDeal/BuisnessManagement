<?php

/**
 * Description of work
 *
 * @author Faizan Ayubi
 */
class Work extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_details;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_project_id;
}
