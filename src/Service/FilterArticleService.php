<?php 

namespace App\Service;

use DateTimeZone;
use App\Entity\ProductStatus;
use Symfony\Component\Validator\Constraints\DateTime;


class FilterArticleService{

    // public function __construct($time = 'now', ?DateTimeZone $now = null) {
    //     $this->time = $time;
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
    // public $expired = 'now';


}