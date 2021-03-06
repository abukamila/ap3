<?php

class ItemController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('deny', // deny guest
                'users' => array('guest'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionTambah()
    {
        $this->layout = '//layouts/box_kecil';
        $model = new AuthItem;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['AuthItem'])) {
            $authItem = $_POST['AuthItem'];
            $auth = Yii::app()->authManager;
            switch ($authItem['type']) {
                case 0:
                    $auth->createOperation($authItem['name'], $authItem['description'], $authItem['bizrule']);
                    break;
                case 1:
                    $auth->createTask($authItem['name'], $authItem['description'], $authItem['bizrule']);
                    break;
                case 2:
                    $auth->createRole($authItem['name'], $authItem['description'], $authItem['bizrule']);
                    break;
            }
            $this->redirect(array('ubah', 'id' => $authItem['name']));
        }

        $this->render('tambah', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUbah($id)
    {
        $model = $this->loadModel($id);

        $child = new AuthItemChild('search');
        $child->unsetAttributes();
        $child->setAttribute('parent', '=' . $id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['AuthItem'])) {
            $model->attributes = $_POST['AuthItem'];
            $model->save();
        }

        $this->render('ubah', array(
            'model' => $model,
            'child' => $child,
            'authItem' => $this->_listAuthItem($id),
        ));
    }

    public function actionListAuthItem($id)
    {
        $this->renderPartial('_authitem_opt', array(
            'authItem' => $this->_listAuthItem($id)
        ));
    }

    public function _listAuthItem($id)
    {
        $authItem = array();
        $authItem['role'] = AuthItem::model()->listAuthItem(2, $id);
        $authItem['task'] = AuthItem::model()->listAuthItem(1, $id);
        $authItem['operation'] = AuthItem::model()->listAuthItem(0, $id);
        return $authItem;
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionHapus($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

    /**
     * Manages all models.
     */
    public function actionIndex()
    {
        $model = new AuthItem('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['AuthItem']))
            $model->attributes = $_GET['AuthItem'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return AuthItem the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = AuthItem::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param AuthItem $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'auth-item-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Assign a Child
     */
    public function actionAssign($id)
    {
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'auth-child-grid' && isset($_POST['child'])) {
            $child = $_POST['child'];
            $auth = Yii::app()->authManager;
            $auth->addItemChild($id, $child);
            echo 'Assign Child Status: OK';
        }
    }

    /*
     * Revoke a child
     */

    public function actionRemove($id, $child)
    {
        echo $id, '--', $child . '. ';
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'auth-child-grid') {
            $auth = Yii::app()->authManager;
            $auth->removeItemChild($id, $child);
            echo 'Remove Child Status: OK';
        }
    }

    public function renderLinkToUbah($data)
    {
        return '<a href="' .
                $this->createUrl($this->id . '/ubah?id=' . $data->name) . '">' .
                $data->name . '</a>';
    }

}
