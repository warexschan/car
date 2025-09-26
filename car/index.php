<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("");

$APPLICATION->IncludeComponent(
    'custom:hl.carBooking',
    '.default',
    [
        "CACHE_TIME" => 0,
        "CACHE_TIME_CAR" => 3600,
        "CURRENT_URL" => $APPLICATION->GetCurPage()
    ]
);

// require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
