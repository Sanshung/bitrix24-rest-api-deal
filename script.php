<?
CrmB24::SetUtm();

class CrmB24
{
    public static function SetUtm()
    {
        $keys = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term');
        foreach ($keys as $row) {
            if ( ! empty($_GET[$row])) {
                $value = strval($_GET[$row]);
                $value = stripslashes($value);
                $value = htmlspecialchars_decode($value, ENT_QUOTES);
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES);
                setcookie('utm[' . $row . ']', $value, 0, '/');
            }
        }
    }
    
    public static function AddDealB24($arFields)
    {
        return self::AddLeadB24($arFields);
        
        $update_data = [
            "fields" => [
                "TITLE"                => strip_tags($arFields['FORM_NAME']),
                // название лида
                "NAME"                 => $arFields['NAME'],
                'UF_CRM_RS_GOOGLE_CID' => self::gaParseCookie(),
                //CID (ID клиента Google analytics)
                //'UF_CRM_1651555096'    => self::yaParseCookie(),
                //Ya_id (ID клиента Яндекс метрики)
                "CONTACT_ID"           => self::AddContact($arFields),
                'UTM_TERM'             => $_COOKIE['utm']['utm_term'],
                'UTM_SOURCE'           => $_COOKIE['utm']['utm_source'],
                'UTM_MEDIUM'           => $_COOKIE['utm']['utm_medium'],
                'UTM_CAMPAIGN'         => $_COOKIE['utm']['utm_campaign'],
                'UTM_CONTENT'          => $_COOKIE['utm']['utm_content'],
                'COMMENTS'             => $arFields['TEXT'],
                "STATUS_ID"            => "NEW",
                "OPENED"               => "Y",
                "UF_CRM_1657628038081" => [
                    "fileData" => [
                        $_FILES['FILE']['name'],
                        base64_encode(file_get_contents($_FILES['FILE']['tmp_name']))
                    ]
                ],
                // ДОСТУПЕН ВСЕМ
                "SOURCE_ID"            => "WEB",
                "ASSIGNED_BY_ID"       => 1, //ответственый
                "PHONE"                => [
                    [
                        "VALUE"      => $arFields['PHONE'],
                        "VALUE_TYPE" => "WORK"
                    ]
                ],
            ]
        ];
        
        $method = "crm.deal.add";
        $return = self::get_json_api($update_data, $method);
        
    }
    
    public static function AddLeadB24($arFields)
    {
        
        $update_data = [
            "fields" => [
                "TITLE"                => strip_tags($arFields['FORM_NAME']),
                // название лида
                "NAME"                 => $arFields['NAME'],
                'UF_CRM_RS_GOOGLE_CID' => self::gaParseCookie(),
                //CID (ID клиента Google analytics)
                //'UF_CRM_1651555096'    => self::yaParseCookie(),
                //Ya_id (ID клиента Яндекс метрики)
                "CONTACT_ID"           => self::AddContact($arFields),
                'UTM_TERM'             => $_COOKIE['utm']['utm_term'],
                'UTM_SOURCE'           => $_COOKIE['utm']['utm_source'],
                'UTM_MEDIUM'           => $_COOKIE['utm']['utm_medium'],
                'UTM_CAMPAIGN'         => $_COOKIE['utm']['utm_campaign'],
                'UTM_CONTENT'          => $_COOKIE['utm']['utm_content'],
                'COMMENTS'             => $arFields['TEXT'],
                "STATUS_ID"            => "NEW",
                "OPENED"               => "Y",
                "UF_CRM_1646311638932" => [
                    "fileData" => [
                        $_FILES['FILE']['name'],
                        base64_encode(file_get_contents($_FILES['FILE']['tmp_name']))
                    ]
                ],
                // ДОСТУПЕН ВСЕМ
                "SOURCE_ID"            => "WEB",
                "ASSIGNED_BY_ID"       => 16,
                "PHONE"                => [
                    [
                        "VALUE"      => $arFields['PHONE'],
                        "VALUE_TYPE" => "WORK"
                    ]
                ],
            ]
        ];
        
        $method = "crm.lead.add";
        $return = self::get_json_api($update_data, $method);
        
    }
    
    public static function AddContact($arFields): int
    {
        $update_data = [
            "fields" => [
                "NAME"  => $arFields['NAME'],
                "PHONE" => [
                    [
                        "VALUE"      => $arFields['PHONE'],
                        "VALUE_TYPE" => "WORK"
                    ]
                ],
            ]
        ];
        $method = "crm.contact.add";
        $return = self::get_json_api($update_data, $method);
        return $return;
    }
    
    public static function get_json_api($data, $method)
    {
        $url = "https://domain/rest/key/." . $method;
        
        $query = http_build_query($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST           => 1,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $url,
            CURLOPT_POSTFIELDS     => $query
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);
        
        $array_return = $result["result"];
        //dump($data);
        
        return $array_return;
    }
    
    public static function gaParseCookie()
    {
        if ( ! empty($_COOKIE['_ga'])) {
            $tmp = explode('.', $_COOKIE['_ga']);
            $clientid = $tmp[2] . '.' . $tmp[3];
        } else {
            $clientid = "";
        };
        return $clientid;
    }
    
    public static function yaParseCookie()
    {
        if ( ! empty($_COOKIE['yandexuid'])) {
            $clientid = $_COOKIE['yandexuid'];
        } else {
            $clientid = "";
        };
        return $clientid;
    }
}
?>
