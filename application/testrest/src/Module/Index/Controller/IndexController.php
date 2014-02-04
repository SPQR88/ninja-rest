<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 28.11.13
 * Time: 22:37
 */

namespace Module\Index\Controller
{
    class IndexController extends \App\Controller\Base
    {
        /**
         * @Access("public")
         * @return array
         */
        public function indexAction()
        {
            return ["test"];
        }

        /**
         * @param $id
         * @return array
         */
        public function testAction($id)
        {
            return ["test-id" => $id];
        }

        /**
         * @Offline(1)
         * @return array
         */
        public function offAction()
        {

        }

        /**
         * @Access("private")
         */
        public function privateAction()
        {
            return [];
        }
    }
}
