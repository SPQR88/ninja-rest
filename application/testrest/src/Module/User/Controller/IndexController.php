<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 28.11.13
 * Time: 22:37
 */

namespace Module\User\Controller
{
    use \Module\User\Model;
    /**
     * Class IndexController
     * @package Module\User\Controller
     */
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

        /**
         * @param $id
         * @return array
         */
        public function getAction($id)
        {
            $user = Model\User::findFirst($id);

            return ['userName' => $user->name, 'userEmail' => $user->email];
        }

        public function httpCodeAction()
        {
            $this->dispatcher->setParam('status', [404, "not found"]);

            return [];
        }
    }
}
