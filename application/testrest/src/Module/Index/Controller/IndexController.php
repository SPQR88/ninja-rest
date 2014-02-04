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
         * @Access("public")
         * @param $id
         * @return array
         */
        public function testAction($id)
        {
            return ["test-id" => $id];
        }
    }
}
