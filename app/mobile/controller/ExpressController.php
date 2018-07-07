<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 2018/4/12
 * Time: 23:36
 */

namespace app\mobile\controller;


use think\Db;

class ExpressController extends BaseController
{
    public function delivery()
    {
        $deliveyCount = Db::name('express')
            ->group('delivery_id')
            ->field(array('count(id)' => 'amount', 'delivery_id'))
            ->buildSql();

        $deliveries = Db::name('express_delivery')
            ->alias('express_delivery')
            ->join("$deliveyCount delivery_count", 'delivery_count.delivery_id = express_delivery.id', 'LEFT')
            ->where('status', 1)
            ->field(array('express_delivery.*', 'IFNULL(delivery_count.amount, 0)' => 'amount'))
            ->select()
            ->toArray();
        $this->result($deliveries, 1);
    }

    public function express($delivery = 0)
    {
        $where = [];
        if ($delivery != 0) {
            $where['delivery_id'] = $delivery;
        }
        $expresses = Db::name('express')
            ->alias('express')
            ->join('__EXPRESS_DELIVERY__ delivery', 'express.delivery_id = delivery.id')
            ->join('__USER__ user', 'express.user_id = user.id')
            ->where($where)
            ->field(array('express.id', 'express.user_id', 'express.delivery_id', 'express.serial', 'express.status', 'delivery.name' => 'delivery_name', 'user.user_nickname', 'user.mobile'))
            ->order('express.status asc')
            ->select()
            ->toArray();
        $this->result($expresses, 1);
    }
}