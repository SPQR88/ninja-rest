<?php
/**
 * Модель покупок.
 */

namespace Module\Order\Model
{
    /**
     * Class Order
     * @package Module\Order\Model
     */
    class Order extends \App\Model\Base
    {

        /**
         * ID покупки.
         *
         * @var integer
         */
        protected $id;

        /**
         * Статус покупки
         *
         * @var string
         */
        protected $status;

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param int $id
         */
        public function setId($id)
        {
            $this->id = $id;
        }

        /**
         * @return string
         */
        public function getStatus()
        {
            return $this->status;
        }

        /**
         * @param string $status
         */
        public function setStatus($status)
        {
            $this->status = $status;
        }
    }
}
