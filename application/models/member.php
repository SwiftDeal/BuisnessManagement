<?php

/**
 * Description of member
 *
 * @author Faizan Ayubi
 */
class Member extends Shared\Model {
    
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
     * @length 32
     */
    protected $_designation;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_project_id;
}
