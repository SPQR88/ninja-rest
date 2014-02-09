<?php
/**
 * Модель товаров.
 */

namespace Module\Item\Model
{
    /**
     * Class Item
     * @package Module\Item\Model
     */
    class Item extends \App\Model\Base
    {

        /**
         * ID товара.
         *
         * @var integer
         */
        protected $id;

        /**
         * Наименование товара.
         *
         * @var string
         */
        protected $name;

        /**
         * Ссылка изображения товара.
         *
         * @var string
         */
        protected $photoUrl;

        /**
         * Цена товара.
         *
         * @var integer
         */
        protected $sale;

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param mixed $id
         */
        public function setId($id)
        {
            $this->id = $id;
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name)
        {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getPhotoUrl()
        {
            return $this->photoUrl;
        }

        /**
         * @param mixed $photoUrl
         */
        public function setPhotoUrl($photoUrl)
        {
            $this->photoUrl = $photoUrl;
        }

        /**
         * @return mixed
         */
        public function getSale()
        {
            return $this->sale;
        }

        /**
         * @param mixed $sale
         */
        public function setSale($sale)
        {
            $this->sale = $sale;
        }
    }
}
