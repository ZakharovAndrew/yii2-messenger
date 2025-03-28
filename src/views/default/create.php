<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создать чат';
$this->params['breadcrumbs'][] = ['label' => 'Мои чаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="chat-create">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Создать новый чат</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

                    <?= $form->field($model, 'access_type')->dropDownList($model::getAccessTypes()) ?>

                    <?= $form->field($model, 'avatarFile')->fileInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>