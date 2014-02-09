<?php
/**
 * Контроллер товаров.
 */
namespace Module\Item\Controller
{
    use Module\Item\Model\Item;

    /**
     * Class IndexController
     * @package Module\Item\Controller
     */
    class IndexController extends \App\Controller\Base
    {

        /**
         * @return array
         */
        public function itemAction()
        {
            $id   = $this->request->get('id');
            $item = [];

            if (isset($id)) {
                $item = Item::findFirst($id)->toArray();
            }

            return $item;
        }

        /**
         * @return array
         */
        public function itemsAction()
        {
            $request    = $this->request;
            $condition  = $request->get('condition');
            $orderField = $request->get('sort_field', null, 'id');
            $orderType  = $request->get('sort_type', null, 'asc');

            if (!empty($condition)) {
                $items = Item::find([
                    'order' => $orderField
                ])->toArray();
            }

            return $items;
        }
    }
}
