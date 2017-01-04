<?php
/**
 * Created by PhpStorm.
 * User: cybernetics
 * Date: 04.10.2016
 * Time: 10:29
 */

namespace app\models\parser;

use Faker\Provider\DateTime;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\DomCrawler\Crawler;
use Yii;
use yii\base\Exception;
use app\models\table\Source;

class ParserSource
{
    const FIELD_SPEND = ['id_source', 'date', 'date_create', 'spend', 'conversion', 'id_user'];
    const FIELD_BALANCE = ['id_source', 'date', 'date_create', 'money', 'id_user'];

    public static function load($user = null)
    {
        $user = is_null($user) ? Yii::$app->user->id : $user;
        self::revContentSave($user);
        self::contentAdSave($user);
        self::adbladeSave($user);
        self::myLikesSave($user);
        self::mgidSave($user);
        $source = Source::find()
            ->andFilterWhere(['and', ['need_manual' => 1, 'type' => 1]])
            ->all();
        $fields = ['id_source', 'date', 'date_create', 'spend', 'conversion', 'id_user', 'update_manual'];
        foreach ($source as $item) {
            if (!ParserUtils::existAccount($user, $item->attributes['id'])) return null;
            $result = self::parseManual($item['id'], $user);
            try {
                Yii::$app->db->createCommand()
                    ->batchInsert('source_spend', $fields, $result)
                    ->execute();
            } catch (\mysqli_sql_exception $error) {
            }
        }
    }

    public static function revContentRequest($user)
    {
        try {
            $id_source = ParserUtils::getIdSource('source', 'Rev Content');
            if (!ParserUtils::existAccount($user, $id_source)) return null;
            $account = Yii::$app->db->createCommand
            ("SELECT * FROM account WHERE id_source = " . $id_source . "  AND id_user = " . $user)
                ->queryOne();
            $startDate = ParserUtils::getStartDate('source_spend', $id_source, $user)->modify('+1 days');
            $endDate = new \DateTime('now');
            date_add($endDate, date_interval_create_from_date_string('-1 days'));
            if ($startDate > $endDate) return null;
            $client = new Client(['cookies' => true]);
            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $client->request('GET', 'https://www.revcontent.com/login', [
                'cookies' => $jar
            ]);
            $client->request('POST', 'https://www.revcontent.com/login', [
                'cookies' => $jar,
                'form_params' => [
                    'name' => $account['login'],
                    'password' => $account['pass'],
                    'login' => 'Sign In'
                ]
            ]);
            $searchDate = $startDate;
            $resultsBalance = array();
            $resultsSpend = array();
            while ($searchDate < $endDate) {
                $date = $searchDate->format('Y-m-d');
                $request = $client->request('GET', 'https://www.revcontent.com/transactions?filter_status=&start_date=' . $date . '&end_date=' . $date . '&enabled=both&system_status=all&targeting_type=', [
                    'cookies' => $jar
                ]);
                $html = (string)$request->getBody();
                $crawler = new Crawler($html);
                if ($crawler->filter('head > title')->text() == 'Login') {
                    return null;
                }
                $crawler = $crawler->filter('.table-responsive tbody > tr td');
                if (trim($crawler->html()) == 'No records found.') {
                    date_add($searchDate, date_interval_create_from_date_string('+1 days'));
                    continue;
                }
                $resultSpend = $crawler->eq(4)->text();
                $resultSpend = str_replace(',', '', substr(trim($resultSpend), 1));
                $resultBalance = $crawler->last()->text();
                $resultBalance = str_replace(',', '', substr(trim($resultBalance), 1));
                if ($resultSpend == '') {
                    $resultBalance = $crawler->eq(6)->text();
                    $resultBalance = str_replace(',', '', substr(trim($resultBalance), 1));
                    $resultSpend = $crawler->eq(11)->text();
                    $resultSpend = str_replace(',', '', substr(trim($resultSpend), 1));
                }
                $resultsBalance[] = [
                    'id_source' => $account['id_source'],
                    'date' => $date,
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'money' => $resultBalance,
                    'id_user' => $user,
                ];
                $resultsSpend[] = [
                    'id_source' => $account['id_source'],
                    'date' => $date,
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'spend' => $resultSpend,
                    'conversion' => 0,
                    'id_user' => $user,
                ];
                date_add($searchDate, date_interval_create_from_date_string('+1 days'));
            }
            $client->request('GET', 'https://www.revcontent.com/logout');
            return [$resultsBalance, $resultsSpend];
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function taboolaRequest($user)
    {
        try {
            $id_source = ParserUtils::getIdSource('source', 'Taboola');
            if (!ParserUtils::existAccount($user, $id_source)) return null;
            $account = Yii::$app->db->createCommand
            ("SELECT * FROM account WHERE id_source = " . $id_source . "  AND id_user = " . $user)
                ->queryOne();
            $startDate = ParserUtils::getStartDate('source_spend', $id_source, $user)->modify('+1 days');
            $endDate = new \DateTime('now');
            date_add($endDate, date_interval_create_from_date_string('-1 days'));
            if ($startDate > $endDate) return null;
            $client = new Client(['cookies' => true]);
            $client->request('GET', 'https://backstage.taboola.com/backstage/j_spring_security_logout');
            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $request = $client->request('GET', 'https://backstage.taboola.com/backstage/login', [
                'cookies' => $jar
            ]);
            $html = (string)$request->getBody();
            $crawler = new Crawler($html);
            $crawler = $crawler->filter('#serverTime');
            $serverTime = $crawler->attr('value');
            $request = $client->request('POST', 'https://backstage.taboola.com/backstage/j_spring_security_check#', [
                'cookies' => $jar,
                'form_params' => [
                    'serverTime' => $serverTime,
                    'redir' => '',
                    'sig' => '',
                    'j_username' => $account['login'],
                    'j_password' => $account['pass'],
                ]
            ]);
            $request = (string)$request->getBody();
            $resultsSpend = array();
            if ($endDate->diff($startDate)->format("%d") < 7) {
                $interval = new \DateInterval('P7D');
                $searchDate = $endDate - $interval;
            } else {
                $searchDate = $startDate;
            }
            $searchDate = $startDate->format('Y-m-d');
            $endDate = $endDate->format('Y-m-d');
            $request = $client->request('GET',
                'https://backstage.taboola.com/backstage/ajax/1063237/campaigns/campaign-summary/report?' .
                'format=json&id=35p9xlwgzcdt&groupId=campaigns&reportId=campaign-summary&dateStart=' .
                $searchDate . '&dateEnd=' . $endDate . '&dateRangeValue=3&term=last%207%20day&currentDimension=day' .
                '&queryFilter=%5B%7B%22id%22%3A%22campaign_param%22%2C%22operator%22%3A%22equal%22%2C%22value%22%3A%22-1%22%7D%5D&t='
                . ($serverTime + 1431) . '&_=' . $serverTime,
                [
                    'cookies' => $jar
                ]);
            $html = (string)$request->getBody();
            $result = json_decode($html, true)['query']['results'];
            foreach ($result as $item) {
                $dateWork = new \DateTime($item['date']);
                if ($dateWork->format('Y-m-d') < $searchDate) continue;
                $resultsSpend[] = [
                    'id_source' => $account['id_source'],
                    'date' => $dateWork->format('Y-m-d'),
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'spend' => $item['spent'],
                    'conversion' => 0,
                    'id_user' => $user,
                ];
            }
            return $resultsSpend;
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function contentAdRequest($user)
    {
        try {
            $id_source = ParserUtils::getIdSource('source', 'Content.ad');
            if (!ParserUtils::existAccount($user, $id_source)) return null;
            $account = Yii::$app->db->createCommand
            ("SELECT * FROM account WHERE id_source = " . $id_source . "  AND id_user = " . $user)
                ->queryOne();
            $startDate = ParserUtils::getStartDate('source_spend', $id_source, $user)->modify('+1 days');
            $endDate = new \DateTime('now');
            date_add($endDate, date_interval_create_from_date_string('-1 days'));
            if ($startDate > $endDate) return null;
            $client = new Client(['cookies' => true]);
            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $client->request('GET', 'https://app.content.ad/Logoff', [
                'cookies' => $jar
            ]);
            $client->request('GET', 'https://app.content.ad/Login', [
                'cookies' => $jar
            ]);
            $requestReturn = $client->request('GET', 'https://app.content.ad/Token?v=' . time(), [
                'cookies' => $jar
            ]);
            $token = (string)$requestReturn->getBody();
            $posStart = strripos($token, 'return');
            $posEnd = strripos($token, ';') - 1;
            $token = substr($token, $posStart + 6, $posEnd - $posStart - 5);
            $token = str_replace('new String', '', $token);
            $token = str_replace('String.fromCharCode', 'chr', $token);
            $token = str_replace('+', '.', $token);
            $token = str_replace("'", '"', $token);
            $token = eval ('return ' . $token . ';');
            $client->request('POST', 'https://app.content.ad/Login', [
                'cookies' => $jar,
                'form_params' => [
                    'Email' => $account['login'],
                    'Password' => $account['pass'],
                    'RememberMe' => 'false',
                    'Token' => $token,
                ]
            ]);
            $searchDate = $startDate;
            $resultsBalance = array();
            $resultsSpend = array();
            while ($searchDate < $endDate) {
                $date = $searchDate->format('m-d-Y');
                $requestReturn = $client->request('GET', 'https://app.content.ad/Advertiser/AggregateReport/RefreshGraph', [
                    'cookies' => $jar,
                    'form_params' => [
                        'startDate' => $date,
                        'endDate' => $date,
                        'timeGroupingMode' => 'H',
                        '_' => time(),
                    ]
                ]);
                $requestReturn = (string)$requestReturn->getBody();
                $result = json_decode($requestReturn);
                if (gettype($result) != 'object') continue;
                $spend = substr($result->spend, 1);
                $resultsSpend[] = [
                    'id_source' => $account['id_source'],
                    'date' => $searchDate->format('Y-m-d'),
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'spend' => $spend,
                    'conversion' => 0,
                    'id_user' => $user,
                ];
                date_add($searchDate, date_interval_create_from_date_string('+1 days'));
            }
            $requestReturn = $client->request('POST', 'https://app.content.ad/Advertiser/Campaigns', [
                'cookies' => $jar,
            ]);
            $html = (string)$requestReturn->getBody();
            $crawler = new Crawler($html);
            $balance = $crawler->filter('.campaignAccountBalanceTotal')->text();
            $balance = substr($balance, 1);
            $balance = str_replace(',', '', $balance);
            $resultsBalance[] = [
                'id_source' => $account['id_source'],
                'date' => $endDate->format('Y-m-d'),
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'money' => $balance,
                'id_user' => $user,
            ];
            return [$resultsBalance, $resultsSpend];
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function mgidRequest($user)
    {
        try {
            $id_source = ParserUtils::getIdSource('source', 'MGID');
            if (!ParserUtils::existAccount($user, $id_source)) return null;
            $account = Yii::$app->db->createCommand
            ("SELECT * FROM account WHERE id_source = " . $id_source . "  AND id_user = " . $user)
                ->queryOne();
            $startDate = ParserUtils::getStartDate('source_spend', $id_source, $user)->modify('+1 days');
            $endDate = new \DateTime('now');
            date_add($endDate, date_interval_create_from_date_string('-1 days'));
            if ($startDate > $endDate) return null;
            $client = new Client(['cookies' => true]);
            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $client->request('GET', 'https://dashboard.mgid.com/user/signin', [
                'cookies' => $jar,
            ]);
            $client->request('POST', 'https://dashboard.mgid.com/user/signin', [
                'cookies' => $jar,
                'form_params' => [
                    'login' => $account['login'],
                    'password' => $account['pass'],
                    'signin' => 'Login'
                ]
            ]);
            $request = $client->request('GET', 'https://dashboard.mgid.com/advertisers/index/dateInterval/interval/dateStart/' .
                $startDate->format('d.m.Y') . '/dateEnd/' . $endDate->format('d.m.Y') . '/status/allButDeleted', [
                'cookies' => $jar,
            ]);
            $html = (string)$request->getBody();
            $start = strpos($html, '"byDate') + 10;
            $end = strpos($html, ',\"campsCount\"');
            $resultSpend = substr($html, $start, $end - $start);
            $resultSpend = str_replace('\\', '', $resultSpend);
            $resultSpend = json_decode($resultSpend);
            $resultsBalance = array();
            $resultsSpend = array();
            $arrDates = $resultSpend->dates;
            $arrSpend = $resultSpend->wages;
            $request = $client->request('GET', 'https://dashboard.mgid.com/advertisers/balance?status=all&dateInterval=interval&dateStart=' .
                $startDate->format('d/m/Y') . '&dateEnd=' . $endDate->format('d/m/Y'), [
                'cookies' => $jar,
            ]);
            $html = (string)$request->getBody();
            $start = strpos($html, "balance', $.parseJSON") + 23;
            $end = strpos($html, "'));", $start);
            $results = substr($html, $start, $end - $start);
            $results = str_replace('\\', '', $results);
            $results = json_decode($results);
            $results = (array)$results;
            try {
                $i = 0;
                foreach ($results as $key => $value) {
                    $resultsSpend[] = [
                        'id_source' => $account['id_source'],
                        'date' => $arrDates[$i],
                        'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                        'spend' => $arrSpend[$i],
                        'conversion' => 0,
                        'id_user' => $user,
                    ];
                    $resultsBalance[] = [
                        'id_source' => $account['id_source'],
                        'date' => $arrDates[$i],
                        'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                        'money' => ($value),
                        'id_user' => $user,
                    ];
                    $i++;
                }
            } catch (Exception $e) {
                $resultsSpend[] = [
                    'id_source' => $account['id_source'],
                    'date' => $endDate->format('Y-m-d'),
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'spend' => 0,
                    'conversion' => 0,
                    'id_user' => $user,
                ];
                $balanceStart = strpos($html, 'Balance:');
                $balanceStart = strpos($html, '$', $balanceStart) + 1;
                $balanceEnd = strpos($html, '<', $balanceStart) - 1;
                $balance = trim(substr($html, $balanceStart, $balanceEnd - $balanceStart));
                $balance = str_replace(' ', '', $balance);
                $resultsBalance[] = [
                    'id_source' => $account['id_source'],
                    'date' => $endDate->format('Y-m-d'),
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'money' => is_null($balance) ? 0 : $balance,
                    'id_user' => $user,
                ];
                $client->request('GET', 'https://dashboard.mgid.com/user/signout');
                return [$resultsBalance, $resultsSpend];
            }
            if (count($resultsBalance) == 0) {
                $resultsSpend[] = [
                    'id_source' => $account['id_source'],
                    'date' => $endDate->format('Y-m-d'),
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'spend' => 0,
                    'conversion' => 0,
                    'id_user' => $user,
                ];
                $balanceStart = strpos($html, 'Balance:');
                $balanceStart = strpos($html, '$', $balanceStart) + 1;
                $balanceEnd = strpos($html, '<', $balanceStart) - 1;
                $balance = trim(substr($html, $balanceStart, $balanceEnd - $balanceStart));
                $balance = str_replace(' ', '', $balance);
                $resultsBalance[] = [
                    'id_source' => $account['id_source'],
                    'date' => $endDate->format('Y-m-d'),
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'money' => is_null($balance) ? 0 : $balance,
                    'id_user' => $user,
                ];
            }
            $client->request('GET', 'https://dashboard.mgid.com/user/signout', [
                'cookies' => $jar,
            ]);
            return [$resultsBalance, $resultsSpend];
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function adbladeRequest($user)
    {
        try {
            $id_source = ParserUtils::getIdSource('source', 'Adblade');
            if (!ParserUtils::existAccount($user, $id_source)) return null;
            $account = Yii::$app->db->createCommand
            ("SELECT * FROM account WHERE id_source = " . $id_source . "  AND id_user = " . $user)
                ->queryOne();
            $startDate = ParserUtils::getStartDate('source_spend', $id_source, $user)->modify('+1 days');
            $endDate = new \DateTime('now');
            date_add($endDate, date_interval_create_from_date_string('-1 days'));
            if ($startDate > $endDate) return null;
            $client = new Client(['cookies' => true]);
            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $client->request('GET', 'https://adblade.com/control/login', [
                'cookies' => $jar,
            ]);
            $client->request('POST', 'https://adblade.com/control/login', [
                'cookies' => $jar,
                'form_params' => [
                    'email' => $account['login'],
                    'password' => $account['pass'],
                    'remember' => 0
                ]
            ]);
            $searchDate = $startDate;
            $resultsBalance = array();
            $resultsSpend = array();
            while ($searchDate <= $endDate) {
                $date = $searchDate->format('Y-m-d');
                $url = 'https://adblade.com/control/account?standardDateRange_1=0&dateType_1=1&startCalendarDate_1=' . $date .
                    '&endCalendarDate_1=' . $date . '&isPostDateChanged_1=1&isDatePicker_1=1';
                $r = $client->request('GET', $url, ['cookies' => $jar,]);
                $html = (string)$r->getBody();
                $crawler = new Crawler($html);
                $crawlerSearch = $crawler->filter('.stats tbody tr');
                if ('No records found' != $crawlerSearch->getNode(0)->nodeValue) {
                    $crawlerSearch = $crawlerSearch->filter('td');
                    $spend = abs($crawlerSearch->getNode(3)->nodeValue);
                    $balance = $crawlerSearch->getNode(4)->nodeValue;
                } else {
                    $spend = 0;
                    $crawlerSearch = $crawler->filter('.account_page table tr td');
                    $balance = $crawlerSearch->getNode(3)->textContent;
                }
                $balance = substr(str_replace(',', '', $balance), 1);
                $resultsBalance[] = [
                    'id_source' => $account['id_source'],
                    'date' => $date,
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'money' => $balance,
                    'id_user' => $user,
                ];
                $resultsSpend[] = [
                    'id_source' => $account['id_source'],
                    'date' => $date,
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'spend' => $spend,
                    'conversion' => 0,
                    'id_user' => $user,
                ];
                date_add($searchDate, date_interval_create_from_date_string('+1 days'));
            }
            return [$resultsBalance, $resultsSpend];
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function myLikesRequest($user)
    {
        try {
            $id_source = ParserUtils::getIdSource('source', 'Mylikes');
            if (!ParserUtils::existAccount($user, $id_source)) return null;
            $account = Yii::$app->db->createCommand
            ("SELECT * FROM account WHERE id_source = " . $id_source . "  AND id_user = " . $user)
                ->queryOne();
            $startDate = ParserUtils::getStartDate('source_spend', $id_source, $user)->modify('+1 days');
            $endDate = new \DateTime('now');
            date_add($endDate, date_interval_create_from_date_string('-1 days'));
            if ($startDate > $endDate) return null;
            $client = new Client(['cookies' => true]);
            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $client->request('GET', 'http://mylikes.com/logout');
            $client->request('GET', 'http://mylikes.com/login', [
                'cookies' => $jar,
            ]);
            $request = $client->request('POST', 'http://mylikes.com/api/login', [
                'cookies' => $jar,
                'form_params' => [
                    'username' => $account['login'],
                    'password' => $account['pass'],
                    'redirect_url' => '/',
                    'tid' => '0',
                ]
            ]);
            $html = (string)$request->getBody();
            $crawler = new Crawler($html);
            $balance = substr(trim($crawler->filter('.balance_section a div')->eq(0)->text()), 1);
            $resultsBalance[] = [
                'id_source' => $account['id_source'],
                'date' => $endDate->format('Y-m-d'),
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'money' => $balance,
                'id_user' => $user,
            ];
            $client->request('GET', 'http://mylikes.com/advertisers/reporting', [
                'cookies' => $jar,
            ]);
            $searchDate = $startDate;
            $resultsSpend = array();
            while ($searchDate < $endDate) {
                $date = $searchDate->format('Y/m/d');
                $url = 'http://mylikes.com/advertisers/reporting?start_date=' . $date . '&end_date=' . $date . '&split_promos=0';
                $request = $client->request('GET', $url, [
                    'cookies' => $jar,
                ]);
                $html = (string)$request->getBody();
                $crawler = new Crawler($html);
                $filter = substr(trim($crawler->filter('tfoot td')->eq(4)->text()), 1);
                $spend = substr($filter, 0, -2);
                $resultsSpend[] = [
                    'id_source' => $account['id_source'],
                    'date' => $date,
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'spend' => $spend,
                    'conversion' => 0,
                    'id_user' => $user,
                ];
                date_add($searchDate, date_interval_create_from_date_string('+1 days'));
            }
            return [$resultsBalance, $resultsSpend];
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function shareaholicRequest($user) //only autorized
    {
        try {
            $id_source = ParserUtils::getIdSource('source', 'Shareaholic');
            if (!ParserUtils::existAccount($user, $id_source)) return null;
            $account = Yii::$app->db->createCommand
            ("SELECT * FROM account WHERE id_source = " . $id_source . "  AND id_user = " . $user)
                ->queryOne();
            $startDate = ParserUtils::getStartDate('source_spend', $id_source, $user)->modify('+1 days');
            $endDate = new \DateTime('now');
            date_add($endDate, date_interval_create_from_date_string('-1 days'));
            if ($startDate > $endDate) return null;
            $client = new Client(['cookies' => true]);
            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $request = $client->request('GET', 'https://shareaholic.com/login', [
                'cookies' => $jar,
            ]);
            $html = (string)$request->getBody();
            $crawler = new Crawler($html);
            $token = trim($crawler->filter('meta[name="csrf-token"]')->attr('content'));
            $request = $client->request('POST', 'https://shareaholic.com/sessions', [
                'cookies' => $jar,
                'form_params' => [
                    'utf8' => '?',
                    'authenticity_token' => $token,
                    'return_to' => '',
                    'alt' => '',
                    'username_or_email' => $account['login'],
                    'password' => $account['pass'],
                    'chrome_id' => '',
                    'commit' => 'Log+In',
                ]
            ]);
            $client->request('GET', 'https://shareaholic.com/logout');
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function parseManual($id_source, $user)
    {
        try {
            $endDate = new \DateTime();
            date_add($endDate, date_interval_create_from_date_string('-1 days'));
            $startDate = ParserUtils::getStartDate('source_spend', $id_source, $user)->modify('+1 days');
            $searchDate = $startDate;
            $results = array();
            while ((date_format($searchDate, 'y') <= date_format($endDate, 'y')) && (date_format($searchDate, 'm') <= date_format($endDate, 'm'))) {
                $date = $searchDate->format('Y/m/t');
                $results[] = [
                    'id_source' => $id_source,
                    'date' => $date,
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'spend' => 0,
                    'conversion' => 0,
                    'id_user' => $user,
                    'update_manual' => 1,
                ];
                date_add($searchDate, date_interval_create_from_date_string('+1 months'));
            }
            return $results;
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function revContentSave($user)
    {
        try {
            $result = ParserSource::revContentRequest($user);
            if (!is_null($result)) {
                Yii::$app->db->createCommand()
                    ->batchInsert('source_spend', self::FIELD_SPEND, $result[1])
                    ->execute();
                Yii::$app->db->createCommand()
                    ->batchInsert('balance', self::FIELD_BALANCE, $result[0])
                    ->execute();
            }
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function contentAdSave($user)
    {
        try {
            $result = ParserSource::contentAdRequest($user);
            if (!is_null($result)) {
                Yii::$app->db->createCommand()
                    ->batchInsert('source_spend', self::FIELD_SPEND, $result[1])
                    ->execute();
                Yii::$app->db->createCommand()
                    ->batchInsert('balance', self::FIELD_BALANCE, $result[0])
                    ->execute();
            }
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function adbladeSave($user)
    {
        try {
            $result = ParserSource::adbladeRequest($user);
            if (!is_null($result)) {
                Yii::$app->db->createCommand()
                    ->batchInsert('source_spend', self::FIELD_SPEND, $result[1])
                    ->execute();
                Yii::$app->db->createCommand()
                    ->batchInsert('balance', self::FIELD_BALANCE, $result[0])
                    ->execute();
            }
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function myLikesSave($user)
    {
        try {
            $result = ParserSource::myLikesRequest($user);
            if (!is_null($result)) {
                Yii::$app->db->createCommand()
                    ->batchInsert('source_spend', self::FIELD_SPEND, $result[1])
                    ->execute();
                Yii::$app->db->createCommand()
                    ->batchInsert('balance', self::FIELD_BALANCE, $result[0])
                    ->execute();
            }
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function mgidSave($user)
    {
        try {
            $result = ParserSource::mgidRequest($user);
            if (!is_null($result)) {
                Yii::$app->db->createCommand()
                    ->batchInsert('source_spend', self::FIELD_SPEND, $result[1])
                    ->execute();
                Yii::$app->db->createCommand()
                    ->batchInsert('balance', self::FIELD_BALANCE, $result[0])
                    ->execute();
            }
        } catch (Exception $ex) {
            return null;
        }
    }
}