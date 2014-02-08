<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 04.02.14
 * Time: 12:21
 */

namespace Module\User\Model
{

    /**
     * Class User
     * @package Module\User\Model
     */
    class User extends \App\Model\Base
    {

        /**
         * ID пользователя.
         *
         * @var integer
         */
        protected $id;

        /**
         * Email пользователя.
         *
         * @var string
         */
        protected $email;

        /**
         * Паоль пользователя
         *
         * @var string
         */
        protected $password;

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
        public function getEmail()
        {
            return $this->email;
        }

        /**
         * @param string $email
         */
        public function setEmail($email)
        {
            $this->email = $email;
        }

        /**
         * @return string
         */
        public function getPassword()
        {
            return $this->password;
        }

        /**
         * @param string $password
         */
        public function setPassword($password)
        {
            // TODO: Hash password
            $this->password = $password;
        }

    }
}

