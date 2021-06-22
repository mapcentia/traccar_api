<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2021 MapCentia ApS
 * @license    http://www.gnu.org/licenses/#AGPL  GNU AFFERO GENERAL PUBLIC LICENSE 3
 *
 */

namespace app\extensions\traccar_api\model;

use app\inc\Model;


/**
 * Class Traccar
 * @package app\extensions\traccar_api\model
 */
class Traccar extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param object $p
     * @return array<mixed>
     * @throw PDOException
     */
    public function store(object $p): array
    {
        $sql = "INSERT into traccar.positions(
                id ,
                attributes ,
                deviceid,
                type,
                protocol,
                servertime ,
                devicetime ,
                fixtime ,
                outdated ,
                valid ,
                latitude ,
                longitude ,
                altitude ,
                speed ,
                course ,
                address,
                accuracy ,
                network ,
                the_geom 
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,ST_geomfromtext(?,4326)) ON CONFLICT DO NOTHING";
        $res = $this->prepare($sql);
        $res->execute([
            $p->id,
            json_encode($p->attributes),
            $p->deviceId,
            $p->type,
            $p->protocol,
            $p->serverTime,
            $p->deviceTime,
            $p->fixTime,
            empty($p->outdated) ? "f" : "t",
            empty($p->valid) ? "f" : "t",
            $p->latitude,
            $p->longitude,
            $p->altitude,
            $p->speed,
            $p->course,
            $p->address,
            $p->accuracy,
            json_encode($p->network),
            "POINT({$p->longitude} {$p->latitude})",
            ]);
        return [
            "success" => true
        ];
    }
}