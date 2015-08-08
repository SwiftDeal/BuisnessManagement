<?php

/**
 * Description of payment
 *
 * @author Faizan Ayubi
 */
class Payment extends Shared\Model {
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user_id;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_project_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 16
     * 
     * @validate required, alpha, min(1), max(16)
     * @label amount
     */
    protected $_amount;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * 
     * @validate required, min(3), max(100)
     * @label comment
     */
    protected $_comment;
}
