<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Claramente\Base\Models\Iblock\Car as CarModel;

if (! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

class Car extends CBitrixComponent
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function executeComponent(): array
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $dateStart = intval($request->get('dateStart'));
        $dateEnd = intval($request->get('dateEnd'));

        if ($dateStart && $dateEnd) {
            $result = CarModel::getCarByDate($dateStart, $dateEnd);
            dd($result);
        }else{
            echo "Не указан обязательный параметр dateStart или dateEnd";
        }

        /**
         * Если нужен вывод в шаблон компонента
         * $this->IncludeComponentTemplate();
         * return $this->arResult;
         */

        return [];
    }


}

