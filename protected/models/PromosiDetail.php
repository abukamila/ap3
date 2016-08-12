<?php

/**
 * This is the model class for table "promosi_detail".
 *
 * The followings are the available columns in table 'promosi_detail':
 * @property string $id
 * @property string $barang_id
 * @property integer $tipe_diskon_id
 * @property string $nominal
 * @property double $persen
 * @property string $updated_at
 * @property string $updated_by
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property Barang $barang
 * @property User $updatedBy
 */
class PromosiDetail extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'promosi_detail';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('barang_id, tipe_diskon_id, updated_at, updated_by', 'required'),
            array('tipe_diskon_id', 'numerical', 'integerOnly' => true),
            array('persen', 'numerical'),
            array('barang_id, updated_by', 'length', 'max' => 10),
            array('nominal', 'length', 'max' => 18),
            array('created_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, barang_id, tipe_diskon_id, nominal, persen, updated_at, updated_by, created_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'barang' => array(self::BELONGS_TO, 'Barang', 'barang_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'barang_id' => 'Barang',
            'tipe_diskon_id' => 'Tipe Diskon',
            'nominal' => 'Nominal',
            'persen' => 'Persen',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('barang_id', $this->barang_id, true);
        $criteria->compare('tipe_diskon_id', $this->tipe_diskon_id);
        $criteria->compare('nominal', $this->nominal, true);
        $criteria->compare('persen', $this->persen);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('updated_by', $this->updated_by, true);
        $criteria->compare('created_at', $this->created_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PromosiDetail the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {

        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = null; // Trigger current timestamp
        $this->updated_by = Yii::app()->user->id;
        return parent::beforeSave();
    }

}
