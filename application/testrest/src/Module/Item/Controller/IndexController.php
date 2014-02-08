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
            $id        = $this->request->get('id');
            $item = [];

            if (isset($id)) {
                $item = Item::findFirst($id)->toArray();
            }

            return json_encode($item);
        }

        public function itemsAction()
        {
            $request   = $this->request;
            $condition = $request->get('condition');
            $orderField = $request->get('sort_field', null, 'id');
            $orderType  = $request->get('sort_type', null, 'asc');

            if (!empty($condition)) {
                $items = Item::find([
                    'order' => $orderField
                ])->toArray();
            }

            return json_encode($items);
        }
    }
}
