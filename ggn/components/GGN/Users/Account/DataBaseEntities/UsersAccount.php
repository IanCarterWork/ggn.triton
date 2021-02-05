<?php

namespace GGN\Users\Account\DataBaseEntities;




class UsersAccount extends \Database\Entity{

    /**
     * @Type integer, 64
     */
    protected $iD;


    /**
     * @Type string, 128
     */
    protected $secretkey;


    /**
     * @Type string, 128
     */
    protected $publickey;


    /**
     * @Type string, 128
     */
    protected $username;


    /**
     * @Type string, 225, NULL
     */
    protected $email;


    /**
     * @Type float,, 1
     */
    protected $level;


    
    /**
     * @Type datetime,,NULL
     */
    protected $expire;


    
    /**
     * @Type datetime
     */
    protected $updated;

    

    /**
     * @Type datetime
     */
    protected $created;

    

    /**
     * @Type integer, 1, NULL
     */
    protected $avail;

    



}