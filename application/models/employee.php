<?php

/**
 * Description of employee
 *
 * @author Faizan Ayubi
 */
class Employee extends Shared\Model {
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     * 
     * @validate required, min(3), max(45)
     * @label designation
     */
    protected $_designation;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * 
     * @validate required, min(3), max(100)
     * @label details
     */
    protected $_details;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 16
     * 
     * @validate required, alpha, min(1), max(16)
     * @label amount
     */
    protected $_salary;
}
