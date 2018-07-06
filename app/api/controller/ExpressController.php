<?php
/**
 * Created by PhpStorm.
 * User: zhanglei
 * Date: 2018/4/27
 * Time: 19:47
 */

namespace app\api\controller;

use app\api\validate\ExpressValidate;
use cmf\controller\HomeBaseController;
use think\Db;

class ExpressController extends HomeBaseController
{
    public function express($page, $pageSize, $taken = 0, $delivery = 0, $user = 0)
    {
        $where = [];
        if ($delivery != 0) {
            $where['delivery_id'] = $delivery;
        }
        if ($user != 0) {
            $where['user_id'] = $user;
        }
        if ($taken == 0) {
            $where['express.status'] = 0;
        }
        $expresses = Db::name('express')
            ->alias('express')
            ->join('__EXPRESS_DELIVERY__ delivery', 'express.delivery_id = delivery.id')
            ->join('__DELIVERER__ deliverer', 'express.user_id = deliverer.id')
            ->where($where)
            ->field(array(
                'express.id',
                'express.user_id',
                'express.delivery_id',
                'express.serial',
                'express.status',
                'IFNULL(express.original, "")' => 'original',
                'IFNULL(express.express_date, "")' => 'express_date',
                'express.is_large',
                'delivery.name' => 'delivery_name',
                'deliverer.nickname',
                'deliverer.mobile'
            ))
            ->order('express.status asc, express.express_date desc, express.id desc')
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->select()
            ->toArray();
        $this->result($expresses, 1);
    }

    public function getExpressByDelivery($delivery, $page, $pageSize, $taken = 0)
    {
        $where = [];
        if ($delivery != 0) {
            $where['delivery_id'] = $delivery;
        }
        if ($taken == 0) {
            $where['express.status'] = 0;
        }
        $expresses = Db::name('express')
            ->alias('express')
            ->join('__DELIVERER__ deliverer', 'express.user_id = deliverer.id')
            ->where($where)
            ->field(array(
                'express.id',
                'express.user_id',
                'express.delivery_id',
                'express.serial',
                'express.remark',
                'express.status',
                'IFNULL(express.original, "")' => 'original',
                'IFNULL(express.express_date, "")' => 'express_date',
                'express.is_large',
                'deliverer.nickname',
                'deliverer.mobile'
            ))
            ->order('express.status asc, express.express_date desc, express.id desc')
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->select()
            ->toArray();
        $this->result($expresses, 1);
    }

    public function getExpressByUser($user, $page, $pageSize, $taken = 1)
    {
        $where = [];
        if ($user != 0) {
            $where['user_id'] = $user;
        }
        if ($taken == 0) {
            $where['express.status'] = 0;
        }
        $expresses = Db::name('express')
            ->alias('express')
            ->join('__EXPRESS_DELIVERY__ delivery', 'express.delivery_id = delivery.id')
            ->join('__DELIVERER__ deliverer', 'express.deliverer_id = deliverer.id', 'LEFT')
            ->where($where)
            ->field(array(
                'express.id',
                'express.user_id',
                'express.delivery_id',
                'express.serial',
                'express.status',
                'IFNULL(express.original, "")' => 'original',
                'IFNULL(express.express_date, "")' => 'express_date',
                'express.is_large',
                'delivery.name' => 'delivery_name',
                'deliverer.nickname',
                'deliverer.mobile'
            ))
            ->order('express.status asc, express.express_date desc, express.id desc')
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->select()
            ->toArray();
        $this->result($expresses, 1);
    }

    public function getExpressById($id)
    {
        $express = Db::name('express')
            ->alias('express')
            ->join('__EXPRESS_DELIVERY__ delivery', 'express.delivery_id = delivery.id')
            ->join('__DELIVERER__ user', 'express.user_id = user.id')
            ->where('express.id', $id)
            ->field(array('express.*', 'user.nickname', 'user.mobile', 'delivery.name' => 'delivery_name'))
            ->find();
        $this->result($express, 1);
    }

    public function delivery()
    {
        $deliveyCount = Db::name('express')
            ->where('status', 0)
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

    public function take($user, $id, $status)
    {
        if ($status == 1) {
            $result = Db::name('express')->update(array('deliverer_id' => $user, 'deliver_time' => date('Y-m-d H:i:s'), 'id' => $id, 'status' => $status));
            $this->result($this->sendTakeMsg($id), 1, '成功');
        } else {
            $result = Db::name('express')->update(array('deliverer_id' => null, 'deliver_time' => null, 'id' => $id, 'status' => $status));
        }
        if ($result !== false) {
            $this->result('成功', 1);
        } else {
            $this->result('失败', 0);
        }
    }

    public function getDelivery()
    {
        $deliveries = Db::name('express_delivery')->where('status', 1)->select()->toArray();
        $this->result($deliveries, 1);
    }

    public function getDeliveryById($id)
    {
        $delivery = Db::name('express_delivery')->find($id);
        $this->result($delivery, 1);
    }

    public function addExpress()
    {
        $express = $this->request->param();
        $validator = new ExpressValidate();
        if ($validator->check($express)) {
            if (Db::name('express')->insert($express)) {
                $this->result('添加成功', 1);
            } else {
                $this->result('添加失败');
            }
        } else {
            $this->result($validator->getError());
        }
    }

    public function recognize($original)
    {
        $result = array(
            'delivery_id' => 0,
            'serial' => ''
        );
        $deliveries = Db::name('express_delivery')->where('status', 1)->select();
        foreach ($deliveries as $delivery) {
            if (strpos($original, $delivery['keyword']) > 0) {
                $result['delivery_id'] = $delivery['id'];
                break;
            }
        }

        $mode1 = "/凭密码[ ]{0,}[\d-]{1,}/";
        $mode2 = "/凭[ ]{0,}[\d-]{1,}/";
        $mode3 = "/编号[ ]{0,}[\d-]{1,}/";
        $mode4 = "/货号[ ]{0,}[\d-]{1,}/";
        $mode5 = "/暗号：[ ]{0,}[\d-]{1,}/";
        $mode6 = "/提货码（[ ]{0,}[\d-]{1,}/";
        $modeNumber = "/[\d-]{1,}/";
        $serial = [];
        if (preg_match($mode1, $original, $middle)) {
            preg_match($modeNumber, $middle[0], $serial);
        } else if (preg_match($mode2, $original, $middle)) {
            preg_match($modeNumber, $middle[0], $serial);
        } else if (preg_match($mode3, $original, $middle)) {
            preg_match($modeNumber, $middle[0], $serial);
        } else if (preg_match($mode4, $original, $middle)) {
            preg_match($modeNumber, $middle[0], $serial);
        } else if (preg_match($mode5, $original, $middle)) {
            preg_match($modeNumber, $middle[0], $serial);
        } else if (preg_match($mode6, $original, $middle)) {
            preg_match($modeNumber, $middle[0], $serial);
        } else {
            preg_match($modeNumber, $original, $serial);
        }
        if (!empty($serial)) {
            $result['serial'] = $serial[0];
        }
        $this->result($result);
    }

    public function getStatistics($user)
    {
        $submit = Db::name('express')->where('user_id', $user)->count();
        $take = Db::name('express')->where('deliverer_id', $user)->count();
        $this->result(array('submit' => $submit, 'take' => $take));
    }

    public function sendTakeMsg($id)
    {
        $accessToken = $this->getAccessToken();
        $express = Db::name('express')
            ->alias('express')
            ->join('__EXPRESS_DELIVERY__ delivery', 'express.delivery_id = delivery.id')
            ->join('__DELIVERER__ user', 'express.user_id = user.id')
            ->where('express.id', $id)
            ->field(array('express.*', 'user.openid', 'delivery.name' => 'delivery_name'))
            ->find();
        $deliverer = Db::name('deliverer')->find($express['deliverer_id']);
        $params = array(
            'touser' => $express['openid'],
            'template_id' => 'sH4sRT_c_nYDZt6iQU3byXgdjFCcCzuw0zq9HZbxn6g',
            'form_id' => $express['form_id'],
            'data' => array(
                'keyword1' => array('value' => $express['delivery_name']),
                'keyword2' => array('value' => $express['serial']),
                'keyword3' => array('value' => $deliverer['nickname']),
                'keyword4' => array('value' => $express['deliver_time']),
            )
        );
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$accessToken";
        $this->log("express sender: " . $express['openid']);
        return cmf_curl_post($url, $params);
    }

    public function getAccessToken()
    {
        $accessToken = Db::name('option')->where('option_name', 'access_token')->find();
        if (empty($accessToken)) {
            $data = $this->getNewAccessToken();
            $data['token_time'] = date('Y-m-d H:i:s');
            Db::name('option')->insert(array('option_name' => 'access_token', 'option_value' => json_encode($data)));
            return $data['access_token'];
        } else {
            $data = json_decode($accessToken['option_value'], true);
            if (strtotime(date('Y-m-d H:i:s')) - strtotime($data['token_time']) >= intval($data['expires_in'])) {
                $data = $this->getNewAccessToken();
                $data['token_time'] = date('Y-m-d H:i:s');
                $accessToken['option_value'] = json_encode($data);
                Db::name('option')->update($accessToken);
            }
            return $data['access_token'];
        }
    }

    public function getNewAccessToken()
    {
        $params = array(
            'appid' => 'wx87c03bc2789e3178',
            'secret' => '498dbbdb7a158f09d8e8eff7363bb56f',
            'grant_type' => 'client_credential'
        );
        $url = "https://api.weixin.qq.com/cgi-bin/token?" . http_build_query($params);
        $data = json_decode(cmf_curl_get($url), true);
        return $data;
    }

    public function log($info)
    {
        Db::name('debug')->insert(array('info' => $info));
    }

}