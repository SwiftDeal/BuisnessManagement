<?php

/**
 * Description of Attendance
 *
 * @author Faizan Ayubi
 */
class Attendance extends Shared\Model {

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user_id;

    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_start;

    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_end;

}
