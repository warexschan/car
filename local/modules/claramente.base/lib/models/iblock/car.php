<?php

namespace Claramente\Base\Models\Iblock;

use Claramente\Base\Models\CarCategory as CarCategoryHL;
use Claramente\Base\Models\CarRules as CarRulesHL;
use Claramente\Base\Models\CarBooking as CarBookingHL;
use Pago\Bitrix\Models\IModel;
use Pago\Bitrix\Models\Queries\Builder;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;

/**
 * @property string CATEGORY // Категория
 * @property string DRIVER // Водитель
 * @method static Builder|$this query()
 * @method Builder|$this get()
 * @method Builder|$this first()
 * @method Builder|$this whereCategory(mixed $data, string $operator = '') // Категория
 * @method Builder|$this whereDriver(mixed $data, string $operator = '') // Водитель
 */
class Car extends IModel
{
    const CAR_DRIVER_GROUP = 7;


    /**
     * @param $dateStart
     * @param $dateEnd
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getCarByDate($dateStart, $dateEnd): array
    {
        global $USER;
        $result = ['status' => true, 'data' => []];
        if ($USER->getId()) {
            $userCarRules = self::getUserGroupCarRules($USER->GetUserGroupArray());
        } else {
            return ['status' => false, 'message' => 'Доступ запрещен'];
        }

        $carDisallow = self::getDisallowCarBookingByDate($dateStart, $dateEnd);

        $carItems = Car::query()
            ->withCache()
            ->withProperties()
            ->select(['ID', 'NAME', 'DRIVER', 'CATEGORY'])
            ->whereActive(true)
            ->whereId($carDisallow, '!=') // Исключаем забронированные
            ->whereCategory($userCarRules, '=') // Подбираем подходящий класс для сотрудника
            ->order('SORT')
            ->get();


        if ($carItems) {
            $carCategory = self::getCarCategory();
            $carDrivers = self::getCarDrivers();

            foreach ($carItems as $car) {
                $result['data'][$car->ID] = [
                    'ID'       => $car->ID,
                    'NAME'     => $car->NAME,
                    'DRIVER'   => key_exists($car->DRIVER, $carDrivers) ? $carDrivers[$car->DRIVER] : null,
                    'CATEGORY' => self::getCategoryName($car->CATEGORY['ID'], $carCategory),
                ];
            }
        }

        return $result;
    }

    /**
     * Автомобили. Получить водителей
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function getCarDrivers(): array
    {
        $carDrivers = [];
        $result = UserTable::getList([
            'select'  => ['ID', 'NAME', 'EMAIL'],
            'filter'  => [
                '=ACTIVE'          => 'Y',
                '=GROUPS.GROUP_ID' => self::CAR_DRIVER_GROUP
            ],
            'runtime' => [
                // подключаем связь с таблицей user_group
                new \Bitrix\Main\Entity\ReferenceField(
                    'GROUPS',
                    UserGroupTable::class,
                    ['=this.ID' => 'ref.USER_ID'],
                    ['join_type' => 'inner']
                )
            ]
        ]);

        while ($user = $result->fetch()) {
            $carDrivers[$user['ID']] = [
                'ID'    => $user['ID'],
                'NAME'  => $user['NAME'],
                'EMAIL' => $user['EMAIL'],
            ];
        }

        return $carDrivers;
    }

    /**
     * Автомобили. Получить категории
     * @return array
     */
    private static function getCarCategory(): array
    {
        $resultCarCategory = [];
        $carCategory = CarCategoryHL::query()
            ->withCache(3600)
            ->select(['ID', 'UF_NAME'])
            ->getArray();
        foreach ($carCategory as $category) {
            $resultCarCategory[$category['ID']] = ['NAME' => $category['UF_NAME']];
        }

        return $resultCarCategory;
    }

    /**
     * Автомобили. Получить права для заданной группы
     * @param  array  $userGroup
     * @return array
     */
    private static function getUserGroupCarRules(array $userGroup = []): array
    {
        $resultCarRules = [];

        $carRules = self::getCarRules();
        foreach ($userGroup as $group) {
            if (key_exists($group, $carRules)) {
                $resultCarRules = array_merge($resultCarRules, $carRules[$group]);
            }
        }

        return array_unique($resultCarRules);
    }

    /**
     * Автомобили. Получить права доступа пользователей
     * @return array
     */
    private static function getCarRules(): array
    {
        $resultCarRules = [];
        $carRules = CarRulesHL::query()
            ->withCache(3600)
            ->select(['ID', 'UF_GROUP', 'UF_CATEGORY'])
            ->getArray();

        foreach ($carRules as $rules) {
            $resultCarRules[$rules['UF_GROUP']][] = $rules['UF_CATEGORY'];
        }

        return $resultCarRules;
    }

    /**
     * Автомобили. Получить занятые машины в указанные даты
     * @param  int  $dateStart
     * @param  int  $dateEnd
     * @return array
     */
    private static function getDisallowCarBookingByDate(int $dateStart, int $dateEnd): array
    {
        $resultCarBooking = [];
        $carBooking = CarBookingHL::query()
            ->withCache(3600)
            ->select(['ID', 'UF_DATE', 'UF_DATE_END'])
            ->whereUfDate(\Bitrix\Main\Type\DateTime::tryParse($dateEnd, 'Ymd'), '<=')
            ->whereUfDateEnd(\Bitrix\Main\Type\DateTime::tryParse($dateStart, 'Ymd'), '>=')
            ->get();

        foreach ($carBooking as $booking) {
            $resultCarBooking[] = $booking->ID;
        }

        return $resultCarBooking;
    }

    /**
     * Автомобили. Получить название категории по EnumId
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function getCategoryName(mixed $categoryEnumId, array $carCategory): ?string
    {
        if ($categoryEnumId) {
            $categoryId = self::getEnumValue($categoryEnumId);

            return key_exists($categoryId, $carCategory) ? $carCategory[$categoryId]['NAME'] : null;
        }


        return null;
    }

    /**
     * Получить значение Enum по Id
     * @param  int  $id
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private static function getEnumValue(int $id): string
    {
        $valueRow = \Bitrix\Iblock\ElementPropertyTable::getRow([
            'filter' => ['=ID' => $id],
            'select' => ['VALUE']
        ]);


        return $valueRow['VALUE'] ?? '';
    }
}
