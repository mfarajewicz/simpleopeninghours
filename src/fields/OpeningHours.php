<?php
/**
 * SimpleOpeningHours plugin for Craft CMS 3.x
 *
 * Simple plugin for storing place opening hours
 *
 * @link      https://www.gearrilla.com/en/
 * @copyright Copyright (c) 2020 Miroslaw Farajewicz
 */

namespace mfarajewicz\simpleopeninghours\fields;

use mfarajewicz\simpleopeninghours\helpers\OpeningHoursGenerator;
use mfarajewicz\simpleopeninghours\SimpleOpeningHours;
use mfarajewicz\simpleopeninghours\assetbundles\openinghoursfield\OpeningHoursFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * OpeningHours Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Miroslaw Farajewicz
 * @package   SimpleOpeningHours
 * @since     1.0.0
 */
class OpeningHours extends Field
{
    // Public Properties
    // =========================================================================

    const DATA_KEY_START = 'start';
    const DATA_KEY_END = 'end';

    /**
     * Some attribute
     *
     * @var string
     */
    public $someAttribute;
    public $openDuration = 8;
    public $allNightOption = false;
    public $closedOption = false;
    public $fields = [];

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->initComplexFields();
    }

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('simple-opening-hours', 'OpeningHours');
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['openDuration', 'integer', 'min' => 0, 'max' => 24],
            [['allNightOption', 'closedOption'], 'boolean'],
            [['fields'], 'safe']
        ]);
    }

    /**
     * Returns the column type that this field should get within the content table.
     *
     * @return string The column type. [[\yii\db\QueryBuilder::getColumnType()]] will be called
     * to convert the give column type to the physical one. For example, `string` will be converted
     * as `varchar(255)` and `string(100)` becomes `varchar(100)`. `not null` will automatically be
     * appended as well.
     * @see \yii\db\QueryBuilder::getColumnType()
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return $value;
    }

    /**
     * @param mixed $value The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     * @return mixed The serialized field value
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * Returns the component’s settings HTML.
     * @return string|null
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'simple-opening-hours/_components/fields/OpeningHours_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * Returns the field’s input HTML.
     * @param mixed                 $value           The field’s value. This will either be the [[normalizeValue() normalized value]],
     *                                               raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element         The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(OpeningHoursFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            'openingHoursPluginSettings' => [
                'openDuration' => $this->openDuration,
            ]
            ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').SimpleOpeningHoursOpeningHours(" . $jsonVars . ");");

        $this->transformData($value);
        return Craft::$app->getView()->renderTemplate(
            'simple-opening-hours/_components/fields/OpeningHours_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'availableHours' => OpeningHoursGenerator::getHours(),
                'days' => OpeningHoursGenerator::getDays()
            ]
        );
    }

    /**
     * Fixes structure retrieved after POST request of settings. Checkboxes are not passed as boolean
     * what's more they are not even passed as 0/1. But either as value "1" or not value at all.
     *
     * @param $value
     */
    private function transformData($value) {
        if (is_array($value)){
            foreach ($value as $i => $dayValue) {
                $this->fields[$i] = $dayValue;
            }
        }
    }

    /**
     * Initialize structure keeping days data
     */
    private function initComplexFields() {
        for ($i = 0; $i < 7; $i++) {
            $this->fields[]= [
                self::DATA_KEY_START => null,
                self::DATA_KEY_END => null
            ];
        }
    }
}
