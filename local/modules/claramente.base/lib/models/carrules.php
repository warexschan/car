<?php

namespace Claramente\Base\Models;

use Pago\Bitrix\Models\HlModel;
use Bitrix\Main\Type\DateTime;
use Pago\Bitrix\Models\Queries\Builder;

/**
 * HighloadBlock - CarRules
 * @property int ID
 * @property string UF_CATEGORY // CAR_CATEGORY
 * @property string UF_GROUP // USER_GROUP
 * @method static Builder|$this query()
 * @method Builder|$this get()
 * @method Builder|$this first()
 * @method Builder|$this whereId(mixed $data, string $operator = '') // ID
 * @method Builder|$this whereUfCategory(mixed $data, string $operator = '') // CAR_CATEGORY
 * @method Builder|$this whereUfGroup(mixed $data, string $operator = '') // USER_GROUP
 */
class CarRules extends HlModel
{

}
