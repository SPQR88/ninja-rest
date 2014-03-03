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

        public function indexAction()
        {
            return ['item entity'];
        }

        public function itemAction()
        {
            $item = [];
            $model = new Item();
            if ($this->request->isPost()) {
                $data = $this->request->getPost();

                return $data;
            }

            if ($this->request->isPut()) {
                $model->title = '123';
                $model->bla   = '1';
                if ($model->save()) {
                    return ['ok'];
                }
            } else {
                $id = $this->request->get('id');
                if (isset($id)) {
                    $item = $model->findFirst($id)->toArray();
                }
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

//            if (!empty($condition)) {
                $items = Item::find()->toArray();
//            }

            return $items;
        }

        public function photoAction()
        {
            $request = $this->request;

            if ($request->hasFiles()) {
                $file = $request->getUploadedFiles();
                var_dump($file);exit;
            }
        }
    }
}
