<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2021 MapCentia ApS
 * @license    http://www.gnu.org/licenses/#AGPL  GNU AFFERO GENERAL PUBLIC LICENSE 3
 *
 */

namespace app\extensions\traccar_api\controller;

use app\conf\App;
use app\inc\Controller;
use app\extensions\traccar_api\model\Traccar as Model;
use app\inc\Route;
use app\models\Database;
use PDOException;
use TypeError;
use Postmark\PostmarkClient;
use Postmark\Models\PostmarkException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\GuzzleException;


/**
 * Class Traccar
 * @package app\extensions\traccar_api\controller
 */
class Traccar extends Controller
{
    /**
     * @return array<string|int|bool>
     */
    public function get_index(): array
    {
        $db =Route::getParam("db");
        Database::setDb($db);
        $m = new Model();
        $client = new Client([
            'timeout' => 10.0,
            'base_uri' => App::$param["traccar"][$db]["baseUri"],
        ]);
        try {
            $res = $client->request("GET", '/api/session', ['query' => ['token' => App::$param["traccar"][$db]["token"]]]);
        } catch (GuzzleException $e) {
            return [
                "success" => false,
                "message" => $e->getMessage(),
            ];
        }
        $cookie = SetCookie::fromString($res->getHeader("Set-Cookie")[0]);

        $arr[$cookie->getName()] = $cookie->getValue();
        $jar = CookieJar::fromArray($arr, App::$param["traccar"][$db]["host"]);

        try {
            $res = $client->request("GET", '/api/positions', ['cookies' => $jar]);
        } catch (GuzzleException $e) {
            return [
                "success" => false,
                "message" => $e->getMessage(),
            ];
        }
        $data = json_decode($res->getBody()->getContents());
        foreach ($data as $p) {
            try {
                $m->store($p);
            } catch (TypeError | PDOException $err) {
                return [
                    "code" => "500",
                    "success" => false,
                    "message" => $err->getMessage(),
                ];
            }
        }

        // Try to send a mail

        // Everything is OK - sending 200 with result
        return [
            "code" => 200,
            "success" => true,
            "positions" => $data,
        ];
    }
}