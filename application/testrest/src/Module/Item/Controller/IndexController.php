<?php
/**
 * Контроллер товаров.
 */
namespace Module\Item\Controller
{
    use Module\Item\Model\Item;

    class IndexController extends \App\Controller\Base
    {
        /**
         * @Access("public")
         * @return array
         */
        public function indexAction()
        {
            return ["item"];
        }

        public function itemAction()
        {

        }
    }
}
