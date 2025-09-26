<?php

namespace Claramente\Base\Models;

use Pago\Bitrix\Models\HlModel;
use Bitrix\Main\Type\DateTime;
use Pago\Bitrix\Models\Queries\Builder;

/**
 * HighloadBlock - CarBooking
 * @property int ID
 * @property string UF_CAR_ID // CAR_ID
 * @property Date UF_DATE // Дата начала бронирования
 * @property Date UF_DATE_END // Дата окончания бронирования
 * @property string UF_USER_ID // USER_ID
 * @method static Builder|$this query()
 * @method Builder|$this get()
 * @method Builder|$this first()
 * @method Builder|$this whereId(mixed $data, string $operator = '') // ID
 * @method Builder|$this whereUfCarId(mixed $data, string $operator = '') // CAR_ID
 * @method Builder|$this whereUfDate(mixed $data, string $operator = '') // Дата начала бронирования
 * @method Builder|$this whereUfDateEnd(mixed $data, string $operator = '') // Дата окончания бронирования
 * @method Builder|$this whereUfUserId(mixed $data, string $operator = '') // USER_ID
 */
class CarBooking extends HlModel
{

}
