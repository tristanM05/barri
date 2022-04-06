<?php 

namespace App\Service;

use DateTimeZone;
use App\Entity\ProductStatus;
use Symfony\Component\Validator\Constraints\DateTime;


class FilterArticleService{

    // public function __construct() {
    //     $now = new \DateTime('now');
    //     $now->setTime(0,0,0);
    // }

    /**
     * @var integer
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var string
     */
    public $n = '';

    /**
     * @var boolean
     */
    public $isvisible = true;

    /**
     * @var ProductStatus[]
     */
    public $status = [];

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;

    // /**
    //  * @var Date
    //  */
    // public $expired = new \DateTime('now');


}