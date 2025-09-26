<?php

namespace Claramente\Base\Models;

use Pago\Bitrix\Models\HlModel;
use Bitrix\Main\Type\DateTime;
use Pago\Bitrix\Models\Queries\Builder;

/**
 * HighloadBlock - CarCategory
 * @property int ID
 * @property string UF_NAME // Название
 * @method static Builder|$this query()
 * @method Builder|$this get()
 * @method Builder|$this first()
 * @method Builder|$this whereId(mixed $data, string $operator = '') // ID
 * @method Builder|$this whereUfName(mixed $data, string $operator = '') // Название
 */
class CarCategory extends HlModel
{

}
