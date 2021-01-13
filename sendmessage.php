<?php
require_once __DIR__ . '/vendor/autoload.php';
include "MyDb.php";
$botToken = "1490734876:AAFtl7zda1GXUOX4Y_NyTvEDIuHBjp2-56g";

/**
 * @var $bot \TelegramBot\Api\Client | \TelegramBot\Api\BotApi
*/

$bot = new \TelegramBot\Api\Client($botToken);
$bot->command('start', static function (\TelegramBot\Api\Types\Message $message) use ($bot) {
    $link = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([[['text' => "O'zbek", 'callback_data' => "uz"], ['text' => 'Ўзбек', 'callback_data' => "cy"]], [['text' => 'Русский', 'callback_data' => "rus"]]]);
    $bot->sendMessage($message->getChat()->getId(), "<b>🇺🇿 Iltimos tilni tanlang!\n\n🇺🇿 Илтимос тилни танланг!\n\n🇷🇺 Пожалуйста, выберите язык!</b>", "HTML", false, null, $link);
});
$bot->command('search', static function (\TelegramBot\Api\Types\Message $message) use ($bot) {
    $bot->sendMessage($message->getChat()->getId(), "Assalomu alaykum! \nSiz /search xizmati orqali o'zingiz izlayotgan universitet haqida tezroq malumot olishingiz mumkin. Masalan Andijon so'zi qatnashgan universitetlarni topish uchun Andijon so'zini kiriitng. ");
});
$bot->callbackQuery(static function (\TelegramBot\Api\Types\CallbackQuery $query) use ($bot) {
    $chatId = $query->getMessage()->getChat()->getId();
    $data = $query->getData();
    $messageId = $query->getMessage()->getMessageId();
    $tilMassiv = ['uz' => 'name_uz_latin', 'rus' => 'name_ru', 'cy' => 'name_uz_cyrill'];
    $til = tilniTop()[$chatId];
    if ($data == "menu") {
        $link = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([[['text' => "O'zbek", 'callback_data' => "uz"], ['text' => 'Ўзбек', 'callback_data' => "cy"]], [['text' => 'Русский', 'callback_data' => "rus"]]]);
        $bot->editMessageText($chatId, $messageId, "<b>🇺🇿 Iltimos tilni tanlang!\n\n🇺🇿 Илтимос тилни танланг!\n\n🇷🇺 Пожалуйста, выберите язык!</b>", "HTML", false, $link);
    }
    if (array_key_exists($data, $tilMassiv)) {
        tilniYoz($chatId, $data);
        $viloyatHeader = $data == "uz" ? "<b>Viloyatni tanlang</b>" : $viloyatHeader = $data == "rus" ? "<b>Выберите область</b>" : "<b>Вилойатни танланг</b>";
        $link = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(tableToMassiv("SELECT * FROM regions", $tilMassiv[$data], 'menu', 'region_id'));
        $bot->editMessageText($chatId, $messageId, $viloyatHeader, "HTML", false, $link);
    }
    $viloyatMassiv = massiv("select * from regions");
    if (in_array($data, $viloyatMassiv)) {
        $a = $tilMassiv[$til];
        $uni_header = variable2($til, "Universitetni tanlang", "Университетни танланг", "Выберите университет", 1);
        if ($GLOBALS['tanlov'] == 1) {
        } else {
            $uni_header2 = massiv("select $a from universities where region_id=$data", $GLOBALS['tanlov']);
            $uni_header .= "\n\n" . $uni_header2[0];
        }
        $link = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(tableToMassiv("SELECT * FROM universities where region_id = $data", $tilMassiv[$til], $til, 'uni_id', $GLOBALS['tanlov']));
        $bot->editMessageText($chatId, $messageId, $uni_header, "HTML", false, $link);
    }
    $universitetMassiv = massiv("select * from universities");
    if (in_array($data, $universitetMassiv)) {
        $a = $tilMassiv[$til];
        $mutahasislik = variable2($til, "Mutaxasislikni tanlang", "Мутахасисликни танланг", "Выберите специальность", 1);
        if ($GLOBALS['tanlov'] == 1) {
        } else {
            $tanlov2 = fakultet($data, $a);
            $message = "";
            foreach ($tanlov2 as $item => $str) {
                $probel = strlen(strval(($item + 1))) == 1 ? "   " : " ";
                $message .= "<b>" . ($item + 1) . ".</b>$probel" . $str . "\n";
            }
            $mutahasislik .= "\n\n" . $message;
        }
        $region_id = massiv("select region_id from universities where uni_id = $data");
        $link = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(tableFakultet($data, $tilMassiv[$til], $region_id[0], $speciality_id, $GLOBALS['tanlov']));
        $bot->editMessageText($chatId, $messageId, $mutahasislik, "HTML", false, $link);
    }
    $fakultetMassiv = massiv("select speciality_id from quota_2020");
    if (gettype($data) == "string") {
        $data2 = substr($data, 0, -3);
        if (in_array(intval($data2), $fakultetMassiv)) {
            $uch = substr($data, -3);
            $universitetName = massiv("select $tilMassiv[$til] from universities where uni_id = $uch");
            $specialist_bachelor = massiv("select $tilMassiv[$til] from specialist_bachelor where specialist_id = $data2");
            $quota_all = massiv("select quota_all from quota_2020 where speciality_id = $data2");
            $quota_grant = massiv("select quota_grant from quota_2020 where speciality_id = $data2");
            $quota_contract = massiv("select quota_contract from quota_2020 where speciality_id = $data2");
            $uz_g = massiv("select uz_g from quota_2020 where speciality_id = $data2");
            $ru_g = massiv("select ru_g from quota_2020 where speciality_id = $data2");
            $qq_g = massiv("select qq_g from quota_2020 where speciality_id = $data2");
            $tj_g = massiv("select tj_g from quota_2020 where speciality_id = $data2");
            $kz_g = massiv("select kz_g from quota_2020 where speciality_id = $data2");
            $kg_g = massiv("select kg_g from quota_2020 where speciality_id = $data2");
            $tm_g = massiv("select tm_g from quota_2020 where speciality_id = $data2");
            $uz_c = massiv("select uz_c from quota_2020 where speciality_id = $data2");
            $ru_c = massiv("select ru_c from quota_2020 where speciality_id = $data2");
            $qq_c = massiv("select qq_c from quota_2020 where speciality_id = $data2");
            $tj_c = massiv("select tj_c from quota_2020 where speciality_id = $data2");
            $kz_c = massiv("select kz_c from quota_2020 where speciality_id = $data2");
            $kg_c = massiv("select kg_c from quota_2020 where speciality_id = $data2");
            $tm_c = massiv("select tm_c from quota_2020 where speciality_id = $data2");
            $menu = variable2($til, "menu", "меню", "меню", 2);
            $orqaga = variable2($til, "orqaga", "орқага", "назад", 2);
            $umumiy = variable2($til, "Umumiy qabul kvotasi:", "Умумий қабул квотаси:", "Общая квота на прием:", 2);
            $ta = variable2($til, "ta", "та", "ед", 2);
            $shundan = variable2($til, "shundan:", "шундан:", "из этого:", 2);
            $kishi = variable2($til, "ta", "та", "чел", 2);
            $uz = variable2($til, "o‘zbek guruhi", "ўзбек гуруҳи", "на узбек. группы", 2);
            $ru = variable2($til, "rus guruhi", "рус гуруҳи", "на рус. группы", 2);
            $kz = variable2($til, "qozoq guruhi", "қозоқ гуруҳи", "на казах. группы", 2);
            $qq = variable2($til, "qoraqalpoq guruhi", "қорақалпоқ гуруҳи", "на каракалпак. группы", 2);
            $tj = variable2($til, "tojik guruhi", "тожик гуруҳи", "на таджик. группы", 2);
            $kg = variable2($til, "qirg'iz guruhi", "қирғиз гуруҳи", "на киргиз. группы", 2);
            $tm = variable2($til, "turkman guruhi", "туркман гуруҳи", "на тукрмен. группы", 2);
            $kontrakt = variable2($til, "To‘lov kontrakt asosida:", "Тўлов контракт асосида:", "на платно-контрактной основе:", 2);
            $jumladan = variable2($til, "jumladan", "жумладан", "из этого", 2);
            $grant = variable2($til, "Davlat granti asosida:", "Давлат гранти асосида:", "на основе госуд. гранта:", 2);
            $link = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([[['text' => $orqaga, 'callback_data' => $uch], ['text' => $menu, 'callback_data' => "menu"]]]);
            $bot->editMessageText($chatId, $messageId, "🏛 <b>$universitetName[0]</b>\n\n🎓 <b>$data2</b>  - <i> $specialist_bachelor[0]</i>\n\n<b>$umumiy</b> $quota_all[0] $ta,\n$shundan\n\n🟢<b>$grant</b> $quota_grant[0] $kishi,\n$jumladan,\n🇺🇿 $uz - $uz_g[0] $ta;\n🇷🇺 $ru - $ru_g[0] $ta;\n🇺🇿🟡 $qq - $qq_g[0] $ta;\n🇹🇯 $tj - $tj_g[0] $ta;\n🇰🇿 $kz - $kz_g[0] $ta;\n🇰🇬 $kg - $kg_g[0] $ta;\n🇹🇲 $tm - $kg_g[0] $ta;\n\n💰 <b>$kontrakt</b> $quota_contract[0] $ta,\n$jumladan,\n🇺🇿 $uz - $uz_c[0] $ta;\n🇷🇺 $ru - $ru_c[0] $ta;\n🇺🇿🟡 $qq - $qq_c[0] $ta;\n🇹🇯 $tj - $tj_c[0] $ta;\n🇰🇿 $kz - $kz_c[0] $ta;\n🇰🇬 $kg - $kg_c[0] $ta;\n🇹🇲 $tm - $kg_c[0] $ta;\n", "HTML", false, $link);
        }
    }
});
$bot->on(static function (\TelegramBot\Api\Types\Update $update) use ($bot) {
},
    static function (\TelegramBot\Api\Types\Update $update) use ($bot) {
        try {
            $text = $update->getMessage()->getText();
            $chatId = $update->getMessage()->getChat()->getId();
            $tilMassiv = ['uz' => 'name_uz_latin', 'rus' => 'name_ru', 'cy' => 'name_uz_cyrill'];
            $til = tilniTop()[$chatId];
            $uni_header = variable2($til, "Universitetni tanlang", "Университетни танланг", "Выберите университет", 1);
            $link = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(tableToMassiv("SELECT * FROM universities WHERE universities.$tilMassiv[$til] LIKE '%{$text}%'",$tilMassiv[$til], $til, 'uni_id',1));
            $bot->deleteMessage($chatId,$update->getMessage()->getMessageId()-1);
            $bot->deleteMessage($chatId,$update->getMessage()->getMessageId());
            $bot->sendMessage($chatId, $uni_header, "HTML", false, null, $link);
        }catch (Exception $e){
            $e->getMessage();
        }
    });
$bot->run();
