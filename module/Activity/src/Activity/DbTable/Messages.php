<?php

namespace Activity\DbTable;

use Eva\Db\TableGateway\TableGateway;
use Zend\Stdlib\Parameters;
use Zend\Db\Sql\Expression;

class Messages extends TableGateway
{
    protected $tableName = 'messages';

    protected $primaryKey = 'id';

    public function setParameters(Parameters $params)
    {
        if($params->page){
            $this->enableCount();
        }

        if($params->keyword){
            $keyword = $params->keyword;
            $this->where(function($where) use ($keyword){
                $where->like('content', "%$keyword%");
                return $where;
            });
        }

        if($params->id){
            $this->where(array('id' => $params->id));
        }

        if($params->user_id){
            $this->where(array('user_id' => $params->user_id));
        }

        $orders = array(
            'idasc' => 'id ASC',
            'iddesc' => 'id DESC',
            'idarray' => 'FIELD(id, %s)',
        );
        if($params->order){
            $order = $orders[$params->order];
            if($order){
                if($params->order == 'idarray') {
                    if($params->id && is_array($params->id)){

                        $idArray = $params->id;
                        $order = sprintf($order, implode(',', array_fill(0, count($idArray), Expression::PLACEHOLDER)));
                        $this->order(array(new Expression($order, $idArray)));

                    }
                } else {
                    $this->order($order);
                }
            }
        }

        return $this;
    }
}