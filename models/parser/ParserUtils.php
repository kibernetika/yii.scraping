<?

namespace app\models\parser;

use Yii;
use app\models\table;
use yii\console\Exception;


class ParserUtils
{
    public static function getIdSource($table, $sourceName)
    {
        return $id_source = Yii::$app->db->createCommand
        ("SELECT id FROM " . $table . " WHERE name ='" . $sourceName . "'")
            ->queryScalar();
    }

    public static function getStartDate($sourceTable, $network, $user)
    {
        $where = ' WHERE (id_user=' . $user . ') and (id_source=' . $network . ')';
        $dateFromDB = Yii::$app->db->createCommand
        ("SELECT MAX(`date`) FROM " . $sourceTable . $where)
            ->queryScalar();
        if (empty($dateFromDB)) {
            $date = new \DateTime();
            date_add($date, date_interval_create_from_date_string('-1 week'));
        } else {
            $date = new \DateTime($dateFromDB);
        }
        return $date;
    }

    public static function convertXMLtoArr($xml)
    {
        try {
            $parser = xml_parser_create();
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($parser, $xml, $values);
            xml_parser_free($parser);
            return $values;
        }catch (\Exception $e){
            return null;
        }
    }

    public static function postCURL($url, $postData)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            $result = curl_exec($ch);
        } catch (\Exception $e) {
            throw $e;
        }
        curl_close($ch);
        return $result;
    }

    public static function existAccount($user, $idSource){
        $result = Yii::$app->db->createCommand
        ("SELECT id FROM account WHERE ( id_source='" . $idSource . "' ) and ( id_user='".$user."' );")
            ->queryAll();
        return ( $result ) ? true : false;
    }
}