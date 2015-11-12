<?php

class Aoe_Metrics_Model_Metric_OrderStateCount
{

    public function collect(Varien_Event_Observer $observer)
    {
        $collector = $observer->getCollector(); /* @var $collector Aoe_Metrics_Model_CollectorInterface */
        if (!$collector instanceof Aoe_Metrics_Model_CollectorInterface) {
            throw new InvalidArgumentException('Collector not found');
        }

        $orderResource = Mage::getResourceModel('sales/order'); /* @var $orderResource Mage_Sales_Model_Resource_Order */
        $readConnection = $orderResource->getReadConnection(); /* @var $readConnection Magento_Db_Adapter_Pdo_Mysql */
        $table = $orderResource->getTable('sales/order');

        $query = 'SELECT state, store_id, count(*) as qty FROM '.$table.' GROUP BY state, store_id';
        foreach ($readConnection->fetchAll($query) as $result) {
            $collector->put('Test', $result['qty'], 'Count', array(
                'StoreId' => $result['store_id'],
                'State' => $result['state'],
            ));
        }
    }

}