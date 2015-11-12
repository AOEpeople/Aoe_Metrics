<?php

class Aoe_Metrics_Model_Cron
{

    public function collect()
    {
        $collector = Mage::getModel('Aoe_Metrics/Collector_CloudWatch'); /* @var $collector Aoe_Metrics_Model_CollectorInterface */
        Mage::dispatchEvent('aoemetrics_collect', array('collector' => $collector));
        $collector->flush();
    }

}